<?php
namespace Wizard\Listener;

use Wizard\Wizard;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

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
        $this->listeners[] = $events->attach(Wizard::EVENT_POST_PROCESS_STEP, array($this, 'persistStep'), 100);
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
        if (!isset($sessionContainer->steps)) {
            return;
        }

        $stepName = $step->getName();
        $sessionContainer->steps[$stepName] = $step->toArray();
    }
}