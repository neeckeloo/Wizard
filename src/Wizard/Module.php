<?php
namespace Wizard;

use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $application    = $e->getApplication();
        $serviceManager = $application->getServiceManager();

        $dispatchListener = $serviceManager->get(Listener\DispatchListener::class);
        $dispatchListener->attach($application->getEventManager());
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
