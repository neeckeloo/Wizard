<?php
namespace Wizard;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Session\Container as SessionContainer;

class StepListener implements ListenerAggregateInterface
{
    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @param SessionContainer $sessionContainer
     */
    public function __construct(SessionContainer $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(StepCollection::EVENT_ADD_STEP, array($this, 'restore'), 100);
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param  EventInterface $e
     * @return string
     */
    public function restore(EventInterface $e)
    {
        if (!isset($this->sessionContainer->steps)) {
            return;
        }

        $step = $e->getTarget();
        $stepName = $step->getName();
        if (!isset($this->sessionContainer->steps[$stepName])) {
            return;
        }

        $step->setFromArray($this->sessionContainer->steps[$stepName]);
    }
}