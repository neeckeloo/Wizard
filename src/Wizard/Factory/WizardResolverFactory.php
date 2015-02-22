<?php
namespace Wizard\Factory;

use Wizard\WizardResolver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardResolverFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return WizardResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('Wizard\Config');

        $request = $serviceLocator->get('Request');
        $router  = $serviceLocator->get('Router');

        $routeMatch = $router->match($request);

        return new WizardResolver($routeMatch, $config);
    }
}
