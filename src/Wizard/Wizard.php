<?php
namespace Wizard;

use Wizard\Form\FormFactory;
use Wizard\Step\StepCollection;
use Wizard\Step\StepInterface;
use Wizard\WizardEvent;
use Wizard\Wizard\IdentifierAccessor;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;

class Wizard implements EventManagerAwareInterface, WizardInterface
{
    use EventManagerAwareTrait;

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
     * @var WizardProcessor
     */
    protected $wizardProcessor;

    /**
     * @var IdentifierAccessor
     */
    protected $identifierAccessor;

    /**
     * @var bool
     */
    protected $processed = false;

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
    public function setWizardProcessor(WizardProcessor $processor)
    {
        $this->wizardProcessor = $processor;
        return $this;
    }

    /**
     * @param  IdentifierAccessor $accessor
     * @return self
     */
    public function setIdentifierAccessor(IdentifierAccessor $accessor)
    {
        $this->identifierAccessor = $accessor;
        return $this;
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
            $this->uid = $this->identifierAccessor->getIdentifier($tokenParamName);
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

    public function previousStep()
    {
        $steps       = $this->getSteps();
        $currentStep = $this->getCurrentStep();
        if (!$steps->isFirst($currentStep)) {
            $previousStep = $steps->getPrevious($currentStep);
            $this->wizard->setCurrentStep($previousStep);
        }
    }

    public function nextStep()
    {
        $steps       = $this->getSteps();
        $currentStep = $this->getCurrentStep();
        if (!$steps->isLast($currentStep)) {
            $nextStep = $steps->getNext($currentStep);
            $this->wizard->setCurrentStep($nextStep);
        }
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
        $this->resetViewModelVariables();

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
        $steps       = $this->getSteps();

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
        if ($this->processed) {
            return;
        }

        $this->wizardProcessor
            ->setWizard($this)
            ->process();

        $this->processed = true;
    }

    protected function resetViewModelVariables()
    {
        if ($this->viewModel) {
            $this->viewModel->setVariables([
                'wizard' => $this,
            ], true);
        }
    }

    /**
     * @param ViewModel $model
     */
    public function setViewModel(ViewModel $model)
    {
        $this->viewModel = $model;
        $this->resetViewModelVariables();
    }

    /**
     * {@inheritDoc}
     */
    public function getViewModel()
    {
        if (!$this->viewModel) {
            $this->setViewModel(new ViewModel());
        }

        return $this->viewModel;
    }
}
