<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\WizardResolver;

class WizardResolverFactory
{
    /**
     * @param  ContainerInterface $container
     * @return WizardResolver
     */
    public function createService(ContainerInterface $container)
    {
        $request = $container->get('Request');
        $router  = $container->get('Router');
        $config  = $container->get('Wizard\Config');

        return new WizardResolver($request, $router, $config);
    }
}
