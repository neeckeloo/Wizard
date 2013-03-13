<?php
namespace Wizard;

use Wizard\Exception;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManager;

class Wizard implements WizardInterface
{
    const STEP_FORM_NAME = 'step';
    const SESSION_CONTAINER_PREFIX = 'wizard';

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

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
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var StepCollection
     */
    protected $steps;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @param Request $request
     * @param Response $response
     * @param RouteMatch $routeMatch
     * @param SessionManager $manager
     */
    public function __construct(Request $request, Response $response,
                                RouteMatch $routeMatch, SessionManager $sessionManager = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->routeMatch = $routeMatch;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return SessionContainer
     */
    protected function getSessionContainer()
    {
        if (null === $this->sessionContainer) {
            $this->sessionContainer = new SessionContainer(
                $this->getSessionContainerName(),
                $this->sessionManager
            );
        }

        return $this->sessionContainer;
    }

    /**
     * @return string
     */
    protected function getSessionContainerName()
    {
        return sprintf('%s_%s', self::SESSION_CONTAINER_PREFIX, $this->getUniqueid());
    }

    /**
     * @return string
     */
    protected function getUniqueid()
    {
        if (null === $this->uid) {
            if ($this->routeMatch->getParam('wizard')) {
                $this->uid = $this->routeMatch->getParam('wizard');
            } else {
                $this->uid = md5(uniqid(rand(), true));
            }
        }

        return $this->uid;
    }

    /**
     * @param  string|StepInterface $step
     * @return Wizard
     */
    protected function setCurrentStep($step)
    {
        if ($step instanceof StepInterface) {
            $step = $step->getName();
        }

        if (!$this->getSteps()->has($step)) {
            return $this;
        }

        $this->getSessionContainer()->currentStep = $step;

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
    public function getForm()
    {
        if (null === $this->form) {
            $this->form = new Form();

            $currentStep = $this->getCurrentStep();
            if (!$currentStep) {
                return null;
            }

            $stepForm = $currentStep->getForm();
            if ($stepForm instanceof Form) {
                $stepForm->setName(self::STEP_FORM_NAME);
                $this->form->add($stepForm);
            }

            $buttons = $this->getFormButtons($currentStep);
            foreach ($buttons as $button) {
                $this->form->add($button, array(
                    'priority' => -100,
                ));
            }
        }

        return $this->form;
    }

    /**
     * @param  StepInterface $step
     * @return array
     */
    protected function getFormButtons($step)
    {
        $buttons = array();

        if ($this->getSteps()->getPrevious($step)) {
            $previousButton = new Element\Button('previous');
            $previousButton->setAttributes(array(
                'type'  => 'submit',
            ));
            $previousButton->setLabel('Précédent');
            $buttons[] = $previousButton;
        }

        $submitButton = new Element\Button('submit');
        $submitButton->setAttributes(array(
            'type'  => 'submit',
        ));

        if ($this->getSteps()->getNext($step)) {
            $submitButton->setLabel('Suivant');
        } else {
            $submitButton->setLabel('Valider');
        }
        
        $buttons[] = $submitButton;

        return $buttons;
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
     * @return void
     */
    protected function doRedirect()
    {
        $url = $this->request->getUri()->toString();
        $this->response->getHeaders()->addHeaderLine('Location', $url);
        $this->response->setStatusCode(302);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        if ($this->request->isPost()) {
            return;
        }

        $steps = $this->getSteps();
        $currentStep = $this->getCurrentStep();
        
        $post = $this->request->getPost();
        if (isset($post['previous']) && !$steps->isFirst($currentStep)) {
            $previousStep = $steps->getPrevious($currentStep);
            $this->setCurrentStep($previousStep);
        } else {
            $complete = $currentStep->process($post[self::STEP_FORM_NAME]);
            if ($complete) {
                if ($steps->isLast($currentStep)) {
                    $this->complete();
                } else {
                    $nextStep = $steps->getNext($currentStep);
                    $this->setCurrentStep($nextStep);
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function complete()
    {

    }
}