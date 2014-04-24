<?php
namespace Wizard\Factory;

use Wizard\StepPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StepPluginManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return StepPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configInstance = new Config($config['wizard_steps']);

        $stepPluginManager = new StepPluginManager($configInstance);
        $stepPluginManager->setServiceLocator($serviceLocator);

        return $stepPluginManager;
    }
}