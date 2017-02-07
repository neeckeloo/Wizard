<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;

class ConfigFactory
{
    /**
     * @param ContainerInterface $container
     * @return array
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        return $config['wizard'];
    }
}