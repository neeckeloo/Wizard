<?php
namespace Wizard;

use Wizard\Exception;
use Wizard\Form\FormFactory;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as Renderer;

class Wizard implements WizardInterface
{
    const STEP_FORM_NAME = 'step';
    const SESSION_CONTAINER_PREFIX = 'wizard';
    const TOKEN_PARAM_NAME = 'uid';

    const EVENT_COMPLETE = 'wizard-complete';
    const EVENT_PRE_PROCESS_STEP = 'step-pre-process';
    const EVENT_POST_PROCESS_STEP = 'step-post-process';

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var WizardOptionsInterface
     */
    protected $options;

    /**
     * @var StepCollection
     */
    protected $steps;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @var bool
     */
    protected $processed = false;

    /**
     * {@inheritDoc}
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFormFactory(FormFactory $factory)
    {
        $this->formFactory = $factory;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
    }

    /**
     * @return SessionContainer
     */
    protected function getSessionContainer()
    {
        if (null === $this->sessionContainer) {
            $this->sessionContainer = new SessionContainer(
                $this->getSessionContainerName()
            );
        }

        return $this->sessionContainer;
    }

    /**
     * @return string
     */
    protected function getSessionContainerName()
    {
        return sprintf('%s_%s', self::SESSION_CONTAINER_PREFIX, $this->getUniqueId());
    }

    /**
     * @return string
     */
    protected function getUniqueId()
    {
        if (null === $this->uid) {
            if (isset($this->request) && $this->request->getQuery(self::TOKEN_PARAM_NAME)) {
                $this->uid = $this->request->getQuery(self::TOKEN_PARAM_NAME);
            } else {
                $this->uid = md5(uniqid(rand(), true));
            }
        }

        return $this->uid;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions(WizardOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        if (!isset($this->options)) {
            $this->setOptions(new WizardOptions());
        }

        return $this->options;
    }

    /**
     * @param  string|StepInterface $step
     * @return Wizard
     */
    public function setCurrentStep($step)
    {
        if ($step instanceof StepInterface) {
            $step = $step->getName();
        }

        if (!$this->getSteps()->has($step)) {
            return $this;
        }

        $currentStep = $this->getSteps()->get($step);
        $currentStep->init();

        $this->getSessionContainer()->currentStep = $step;
        $this->resetForm();
        $this->initViewModel();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentStep()
    {
        $steps = $this->getSteps();
        if (count($steps) == 0) {
            return null;
        }

        $sessionContainer = $this->getSessionContainer();
        if (!isset($sessionContainer->currentStep) || !$steps->has($sessionContainer->currentStep)) {
            return $steps->getFirst();
        }

        return $steps->get($sessionContainer->currentStep);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentStepNumber()
    {
        $currentStep = $this->getCurrentStep();
        $steps = $this->getSteps();

        $i = 1;
        foreach ($steps as $step) {
            if ($step->getName() == $currentStep->getName()) {
                break;
            }
            $i++;
        }

        return $i;
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {
        $currentStep = $this->getCurrentStep();
        if (!$currentStep) {
            return null;
        }

        if (null === $this->form) {
            $this->form = $this->formFactory->create();
            $this->form->setAttribute('action', sprintf(
                '?%s=%s',
                self::TOKEN_PARAM_NAME,
                $this->getUniqueId()
            ));

            if (!$this->getSteps()->getPrevious($currentStep)) {
                $this->form->remove('previous');
            }

            if (!$this->getSteps()->getNext($currentStep)) {
                $this->form->remove('next');
            } else {
                $this->form->remove('valid');
            }
        }

        $stepForm = $currentStep->getForm();
        if ($stepForm instanceof Form) {
            if ($this->form->has(self::STEP_FORM_NAME)) {
                $this->form->remove(self::STEP_FORM_NAME);
            }

            $stepForm->setName(self::STEP_FORM_NAME);
            $stepForm->populateValues($currentStep->getData());
            $this->form->add($stepForm);
        }

        return $this->form;
    }

    /**
     * return void
     */
    protected function resetForm()
    {
        $this->form = null;
    }

    /**
     * {@inheritDoc}
     */
    public function setSteps(StepCollection $steps)
    {
        $this->steps = $steps;

        $sessionContainer = $this->getSessionContainer();
        $stepListener = new StepListener($sessionContainer);
        $this->steps->getEventManager()->attachAggregate($stepListener);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSteps()
    {
        if (null === $this->steps) {
            $this->setSteps(new StepCollection());
        }

        return $this->steps;
    }

    /**
     * @throws Exception\RuntimeException
     * @return void
     */
    protected function doRedirect()
    {
        $redirectUrl = $this->getOptions()->getRedirectUrl();
        if (null === $redirectUrl) {
            throw new Exception\RuntimeException('You must provide url to redirect when wizard is complete.');
        }

        $this->redirect($redirectUrl);
    }

    /**
     * @throws Exception\RuntimeException
     * @return void
     */
    protected function doCancel()
    {
        $cancelUrl = $this->getOptions()->getCancelUrl();
        if (null === $cancelUrl) {
            throw new Exception\RuntimeException('You must provide url to cancel wizard process.');
        }

        $this->redirect($cancelUrl);
    }

    /**
     * @param  string $url
     * @return void
     */
    protected function redirect($url)
    {
        $this->response->getHeaders()->addHeaderLine('Location', $url);
        $this->response->setStatusCode(302);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        if ($this->processed || !$this->request->isPost()) {
            return;
        }

        $steps = $this->getSteps();
        $currentStep = $this->getCurrentStep();

        $post = $this->request->getPost();
        $values = $post->getArrayCopy();
        if (isset($values['previous']) && !$steps->isFirst($currentStep)) {
            $previousStep = $steps->getPrevious($currentStep);
            $this->setCurrentStep($previousStep);
        } else if (isset($values['cancel'])) {
            $this->doCancel();
        } else {
            $this->getEventManager()->trigger(self::EVENT_PRE_PROCESS_STEP, $currentStep, array(
                'values' => $values,
            ));

            $complete = $currentStep->process($values);
            if (null !== $complete) {
                $currentStep->setComplete($complete);
            }
            $currentStep->setData($values);

            $this->getEventManager()->trigger(self::EVENT_POST_PROCESS_STEP, $currentStep);

            if ($currentStep->isComplete()) {
                if ($steps->isLast($currentStep)) {
                    $this->getEventManager()->trigger(self::EVENT_COMPLETE, $this);
                    $this->doRedirect();
                } else {
                    $nextStep = $steps->getNext($currentStep);
                    $this->setCurrentStep($nextStep);
                }
            }
        }

        $this->processed = true;

        $sessionSteps = array();
        foreach ($steps as $step) {
            $sessionSteps[$step->getName()] = $step->toArray();
        }

        $sessionContainer = $this->getSessionContainer();
        $sessionContainer->steps = $sessionSteps;
    }

    /**
     * @return void
     */
    protected function initViewModel()
    {
        if (!$this->viewModel) {
            return;
        }

        $this->viewModel->setVariables(array(
            'wizard' => $this,
        ), true);
    }

    /**
     * {@inheritDoc}
     */
    public function getViewModel()
    {
        if (!$this->viewModel) {
            $this->viewModel = new ViewModel();
            $this->initViewModel();
        }

        $template = $this->getOptions()->getLayoutTemplate();
        $this->viewModel->setTemplate($template);

        return $this->viewModel;
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $model = $this->getViewModel();
        return $this->renderer->render($model);
    }
}