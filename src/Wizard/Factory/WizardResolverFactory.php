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
    public function __invoke(ContainerInterface $container)
    {
        $request = $container->get('Request');
        $router  = $container->get('Router');
        $config  = $container->get('Wizard\Config');

        return new WizardResolver($request, $router, $config);
    }
}
