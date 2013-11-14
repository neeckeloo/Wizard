<?php
namespace Wizard;

use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\ManagerInterface as SessionManager;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as Renderer;

interface WizardInterface
{
    /**
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * @param  Request $request
     * @return WizardInterface
     */
    public function setRequest(Request $request);

    /**
     * @param  Response $response
     * @return WizardInterface
     */
    public function setResponse(Response $response);

    /**
     * @param  SessionManager $sessionManager
     * @return WizardInterface
     */
    public function setSessionManager(SessionManager $sessionManager);

    /**
     * @param  Renderer $renderer
     * @return WizardInterface
     */
    public function setRenderer(Renderer $renderer);

    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @param  WizardOptionsInterface $options
     * @return WizardInterface
     */
    public function setOptions(WizardOptionsInterface $options);

    /**
     * @return WizardOptionsInterface
     */
    public function getOptions();

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
     * @return WizardInterface
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
     * @return void
     */
    public function complete();

    /**
     * @return ViewModel
     */
    public function getViewModel();

    /**
     * @return string
     */
    public function render();
}