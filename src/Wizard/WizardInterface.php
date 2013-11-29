<?php
namespace Wizard;

use Wizard\Form\FormFactory;
use Wizard\StepInterface;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as Renderer;

interface WizardInterface
{
    /**
     * @param  Request $request
     * @return self
     */
    public function setRequest(Request $request);

    /**
     * @param  Response $response
     * @return self
     */
    public function setResponse(Response $response);

    /**
     * @param  FormFactory $factory
     * @return self
     */
    public function setFormFactory(FormFactory $factory);

    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @return SessionContainer
     */
    public function getSessionContainer();

    /**
     * @param  array|Traversable|WizardOptionsInterface $options
     * @return self
     */
    public function setOptions($options);

    /**
     * @return WizardOptionsInterface
     */
    public function getOptions();

    /**
     * @param  string|StepInterface $step
     * @return self
     */
    public function setCurrentStep($step);

    /**
     * @return StepInterface
     */
    public function getCurrentStep();

    /**
     * @return int
     */
    public function getCurrentStepNumber();

    /**
     * @return Form
     */
    public function getForm();

    /**
     * @param  StepCollection $steps
     * @return self
     */
    public function setSteps(StepCollection $steps);

    /**
     * @return StepCollection
     */
    public function getSteps();

    /**
     * @return int
     */
    public function getTotalStepCount();

    /**
     * @return int
     */
    public function getPercentProgress();

    /**
     * @return void
     */
    public function process();

    /**
     * @return ViewModel
     */
    public function getViewModel();
}