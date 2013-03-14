<?php
namespace Wizard;

use Wizard\Exception;
use Wizard\Form\Element\Button\Previous as PreviousButton;
use Wizard\Form\Element\Button\Next as NextButton;
use Wizard\Form\Element\Button\Valid as ValidButton;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManager;

abstract class AbstractWizard implements WizardInterface
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
     * @param  Request $request
     * @return Wizard
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param  Response $response
     * @return Wizard
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @param  RouteMatch $routeMatch
     * @return Wizard
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * @param  SessionManager $sessionManager
     * @return Wizard
     */
    public function setSessionManager(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
        return $this;
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
            $buttons[] = new PreviousButton();
        }

        if ($this->getSteps()->getNext($step)) {
            $submitButton = new NextButton();
        } else {
            $submitButton = new ValidButton();
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
            $sessionContainer = $this->getSessionContainer();
            if (isset($sessionContainer->steps)) {
                $this->setSteps($sessionContainer->steps);
            } else {
                $this->setSteps(new StepCollection());
            }
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