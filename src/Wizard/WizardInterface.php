<?php
namespace Wizard;

use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\ManagerInterface as SessionManager;

interface WizardInterface
{
    /**
     * @param  Request $request
     * @return Wizard
     */
    public function setRequest(Request $request);

    /**
     * @param  Response $response
     * @return Wizard
     */
    public function setResponse(Response $response);

    /**
     * @param  RouteMatch $routeMatch
     * @return Wizard
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @param  SessionManager $sessionManager
     * @return Wizard
     */
    public function setSessionManager(SessionManager $sessionManager);

    /**
     * @return StepInterface
     */
    public function getCurrentStep();

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
     * @return void
     */
    public function process();
}