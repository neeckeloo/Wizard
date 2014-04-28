<?php
namespace Wizard\Listener;

use Wizard\WizardEvent;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class WizardListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(WizardEvent::EVENT_POST_PROCESS_STEP, array($this, 'persistStep'), 100);
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
    public function persistStep(EventInterface $e)
    {
        $step = $e->getTarget();
        $wizard = $step->getWizard();

        $sessionContainer = $wizard->getSessionContainer();
        if (empty($sessionContainer->steps)) {
            $sessionContainer->steps = array();
        }

        $stepName = $step->getName();
        $sessionContainer->steps[$stepName] = $step->toArray();
    }
}