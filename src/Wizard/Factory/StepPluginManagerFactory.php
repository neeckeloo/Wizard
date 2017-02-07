<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Step\StepPluginManager;

class StepPluginManagerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return StepPluginManager
     */
    public function createService(ContainerInterface $container)
    {
        $config = $container->get('config');

        return new StepPluginManager($container, $config['wizard_steps']);
    }
}