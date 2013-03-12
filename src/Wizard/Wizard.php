<?php
namespace Wizard;

use Wizard\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container as SessionContainer;

class Wizard implements WizardInterface
{
    /**
     * @var string
     */
    protected $uid;

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
     * @param  RouteMatch $routeMatch
     */
    public function __construct(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;

        if ($this->routeMatch->getParam('wizard')) {
            $this->uid = $this->routeMatch->getParam('wizard');
        } else {
            $this->uid = md5(uniqid(rand(), true));
        }
        
        $this->steps = new StepCollection();
    }

    /**
     * @return SessionContainer
     */
    public function getSessionContainer()
    {
        if (!$this->sessionContainer) {
            $this->sessionContainer = new SessionContainer($this->uid);
        }

        return $this->sessionContainer;
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