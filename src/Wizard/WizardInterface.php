<?php
namespace Wizard;

use Wizard\Form\FormFactory;
use Wizard\StepInterface;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;

interface WizardInterface
{
    /**
     * @param  HttpRequest $request
     * @return self
     */
    public function setRequest(HttpRequest $request);

    /**
     * @param  HttpResponse $response
     * @return self
     */
    public function setResponse(HttpResponse $response);

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
     * @return HttpResponse|ViewModel
     */
    public function process();

    /**
     * @return ViewModel
     */
    public function getViewModel();
}
