<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Step\StepFactory;
use Wizard\Step\StepPluginManager;

class StepFactoryFactory
{
    /**
     * @param  ContainerInterface $container
     * @return StepFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        $stepPluginManager = $container->get(StepPluginManager::class);
        $formPluginManager = $container->get('FormElementManager');

        return new StepFactory($stepPluginManager, $formPluginManager);
    }
}
