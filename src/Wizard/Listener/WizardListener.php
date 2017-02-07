<?php
namespace Wizard\Listener;

use Wizard\Step\StepInterface;
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
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(WizardEvent::EVENT_POST_PROCESS_STEP, [$this, 'persistStep'], 100);
    }

    /**
     * @param  EventInterface $e
     * @return string
     */
    public function persistStep(EventInterface $e)
    {
        /** @var StepInterface $step */
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