<?php
namespace Wizard\Listener;

use Wizard\StepCollection;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class StepCollectionListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

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
        $step = $e->getTarget();
        $wizard = $step->getWizard();

        $sessionContainer = $wizard->getSessionContainer();
        if (empty($sessionContainer->steps)) {
            return;
        }

        $stepName = $step->getName();
        if (!isset($sessionContainer->steps[$stepName])) {
            return;
        }

        $step->setFromArray($sessionContainer->steps[$stepName]);
    }
}