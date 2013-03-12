<?php
namespace Wizard;

use Wizard\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container as SessionContainer;

class Wizard implements WizardInterface
{
    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var StepCollection
     */
    protected $steps;

    /**
     * Generates a token to be used for saving
     */
    public function __construct()
    {
        $this->sessionContainer = new SessionContainer(__CLASS__);
        $this->steps = new StepCollection();
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
     * @return StepCollection
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * {@inheritDoc}
     */
    public function process(StepInterface $step)
    {
        if (!$this->getSteps()->has($step)) {
            throw new Exception\RuntimeException(sprintf(
                'The step "%s" does not exists in the wizard.',
                $step->getName()
            ));
        }

        $step->process();
    }
}