<?php
namespace Wizard;

use Wizard\Exception;
use Wizard\Form\FormFactory;
use Wizard\Step\StepCollection;
use Wizard\Step\StepInterface;
use Wizard\WizardEvent;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;

class Wizard implements WizardInterface
{
    const STEP_FORM_NAME = 'step';
    const SESSION_CONTAINER_PREFIX = 'wizard';

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

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

    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse(HttpResponse $response)
    {
        $this->response = $response;
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
     * {@inheritDoc}
     */
    public function getSessionContainer()
    {
        if (null === $this->sessionContainer) {
            $sessionContainerName = sprintf('%s_%s', self::SESSION_CONTAINER_PREFIX, $this->getUniqueId());
            $this->sessionContainer = new SessionContainer($sessionContainerName);
        }

        return $this->sessionContainer;
    }

    /**
     * @return string
     */
    protected function getUniqueId()
    {
        if (null === $this->uid) {
            $tokenParamName = $this->getOptions()->getTokenParamName();
            $tokenValue = $this->request->getQuery($tokenParamName, false);

            if ($tokenValue) {
                $this->uid = $tokenValue;
            } else {
                $this->uid = md5(uniqid(rand(), true));
            }
        }

        return $this->uid;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        if (!$options instanceof WizardOptionsInterface) {
            $options = new WizardOptions($options);
        }
        
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
     * {@inheritDoc}
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
                $this->getOptions()->getTokenParamName(),
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
     * {@inheritDoc}
     */
    public function getTotalStepCount()
    {
        $steps = $this->getSteps();
        return count($steps);
    }

    /**
     * {@inheritDoc}
     */
    public function getPercentProgress()
    {
        $stepCount = $this->getTotalStepCount();
        if ($stepCount < 1) {
            return 0;
        }

        return round((($this->getCurrentStepNumber() - 1) / $stepCount) * 100);
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

        return $this->redirect($redirectUrl);
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

        return $this->redirect($cancelUrl);
    }

    /**
     * @param  string $url
     * @return void
     */
    protected function redirect($url)
    {
        $this->response->getHeaders()->addHeaderLine('Location', $url);
        $this->response->setStatusCode(302);
        
        return $this->response;
    }
    
    public function init()
    {
        $wizardEvent = new WizardEvent();
        $wizardEvent->setWizard($this);
                
        $this->getEventManager()->trigger(WizardEvent::EVENT_INIT, $wizardEvent);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        if ($this->processed || !$this->request->isPost()) {
            return;
        }

        $this->processed = true;

        $steps = $this->getSteps();
        $currentStep = $this->getCurrentStep();

        $post = $this->request->getPost();
        $values = $post->getArrayCopy();
        if (isset($values['previous']) && !$steps->isFirst($currentStep)) {
            $previousStep = $steps->getPrevious($currentStep);
            $this->setCurrentStep($previousStep);            
            return;
        }
        
        if (isset($values['cancel'])) {
            return $this->doCancel();
        }        
        
        $this->getEventManager()->trigger(WizardEvent::EVENT_PRE_PROCESS_STEP, $currentStep, array(
            'values' => $values,
        ));

        $complete = $currentStep->process($values);
        if (null !== $complete) {
            $currentStep->setComplete($complete);
        }
        $currentStep->setData($values);

        $this->getEventManager()->trigger(WizardEvent::EVENT_POST_PROCESS_STEP, $currentStep);

        if ($currentStep->isComplete()) {
            if ($steps->isLast($currentStep)) {
                $wizardEvent = new WizardEvent();
                $wizardEvent->setWizard($this);

                $this->getEventManager()->trigger(WizardEvent::EVENT_COMPLETE, $wizardEvent);
                
                return $this->doRedirect();
            }

            $nextStep = $steps->getNext($currentStep);
            $this->setCurrentStep($nextStep);
        }
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
    
    public function __destruct()
    {
        $this->getSessionContainer()->options = $this->options->toArray();
        //\Zend\Debug\Debug::dump($this->getSessionContainer()->options);
    }
}
