<?php
namespace Wizard;

use Wizard\Exception;
use Wizard\Form\Element\Button\Previous as PreviousButton;
use Wizard\Form\Element\Button\Next as NextButton;
use Wizard\Form\Element\Button\Valid as ValidButton;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManager;

abstract class AbstractWizard implements WizardInterface, ServiceManagerAwareInterface
{
    const STEP_FORM_NAME = 'step';
    const SESSION_CONTAINER_PREFIX = 'wizard';
    const TOKEN_PARAM_NAME = 'uid';

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
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var StepCollection
     */
    protected $steps;

    /**
     * @var Form
     */
    protected $form;

    /**
     * {@inheritDoc}
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

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
            if ($this->request->getQuery(self::TOKEN_PARAM_NAME)) {
                $this->uid = $this->request->getQuery(self::TOKEN_PARAM_NAME);
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
            $currentStep = $this->getCurrentStep();
            if (!$currentStep) {
                return null;
            }

            $this->form = $this->serviceManager->get('Wizard\Form');
            $this->form->setAttribute('action', sprintf(
                '?%s=%s',
                self::TOKEN_PARAM_NAME,
                $this->getUniqueid()
            ));

            $stepForm = $currentStep->getForm();
            if ($stepForm instanceof Form) {
                $stepForm->setName(self::STEP_FORM_NAME);
                $this->form->add($stepForm);
            }

            if (!$this->getSteps()->getPrevious($currentStep)) {
                $this->form->remove('previous');
            }

            if (!$this->getSteps()->getNext($currentStep)) {
                $this->form->get('next')->setLabel('Valider');
            }
        }

        return $this->form;
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