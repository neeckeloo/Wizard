<?php
namespace Wizard\Factory;

use Wizard\Step\StepFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StepFactoryFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return StepFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $stepPluginManager = $serviceLocator->get('Wizard\Step\StepPluginManager');
        $formPluginManager = $serviceLocator->get('FormElementManager');

        return new StepFactory($stepPluginManager, $formPluginManager);
    }
}
