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
        $request = $serviceLocator->get('Request');
        $router  = $serviceLocator->get('Router');
        $config  = $serviceLocator->get('Wizard\Config');

        return new WizardResolver($request, $router, $config);
    }
}
