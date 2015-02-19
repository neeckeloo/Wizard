<?php
namespace Wizard\Listener;

use Wizard\Step\StepCollection;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class StepCollectionListener implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(StepCollection::EVENT_ADD_STEP, array($this, 'restore'), 100);
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