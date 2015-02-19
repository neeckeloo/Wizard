<?php
namespace Wizard\Listener;

use Wizard\WizardEvent;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class WizardListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(WizardEvent::EVENT_POST_PROCESS_STEP, [$this, 'persistStep'], 100);
    }

    /**
     * @param  EventInterface $e
     * @return string
     */
    public function persistStep(EventInterface $e)
    {
        $step   = $e->getTarget();
        $wizard = $step->getWizard();

        $sessionContainer = $wizard->getSessionContainer();
        if (empty($sessionContainer->steps)) {
            $sessionContainer->steps = [];
        }

        $stepName = $step->getName();
        $sessionContainer->steps[$stepName] = $step->toArray();
    }
}