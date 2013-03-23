<?php
namespace Wizard;

use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\ManagerInterface as SessionManager;
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
     * @param  string $url
     * @return WizardInterface
     */
    public function setRedirectUrl($url);

    /**
     * @return void
     */
    public function process();

    /**
     * @return void
     */
    public function complete();

    /**
     * @return string
     */
    public function render();
}