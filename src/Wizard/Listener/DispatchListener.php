<?php
namespace Wizard\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class DispatchListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 10);
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
     * @param  MvcEvent $e
     * @return void
     */
    public function onDispatch(MvcEvent $e)
    {
        $application = $e->getApplication();        
        $serviceManager = $application->getServiceManager();
        
        $matchedRouteName = $e->getRouteMatch()->getMatchedRouteName();
        
        $config = $serviceManager->get('Wizard\Config');
        
        $wizard = null;
        foreach ($config['wizards'] as $name => $options) {
            if (empty($options['route'])) {
                continue;
            }
            
            if (is_string($options['route'])) {
                $options['route'] = array($options['route']);
            }
            
            if (!in_array($matchedRouteName, $options['route'])) {
                continue;
            }
            
            $wizard = $name;
        }
        
        if (!$wizard) {
            return;
        }
        
        /* @var $wizardFactory \Wizard\WizardFactory */
        $wizardFactory = $serviceManager->get('Wizard\Factory');
        
        /* @var $wizard \Wizard\WizardInterface */
        $instance = $wizardFactory->create($wizard);
        
        return $instance->process();
    }
}