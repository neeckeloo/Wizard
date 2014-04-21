<?php
namespace Wizard\Factory;

use Wizard\StepPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StepPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['wizard']['wizard_steps'];

        $stepPluginManager = new StepPluginManager(new Config($config));
        $stepPluginManager->setServiceLocator($serviceLocator);

        return $stepPluginManager;
    }
}