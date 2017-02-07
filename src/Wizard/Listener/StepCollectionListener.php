<?php
namespace Wizard\Listener;

use Wizard\Step\StepCollection;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class StepCollectionListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(StepCollection::EVENT_ADD_STEP, [$this, 'restore'], 100);
    }

    /**
     * @param  EventInterface $e
     * @return string
     */
    public function restore(EventInterface $e)
    {
        $step   = $e->getTarget();
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