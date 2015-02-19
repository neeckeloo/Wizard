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
        $application = $serviceLocator->get('Application');
        $routeMatch  = $application->getMvcEvent()->getRouteMatch();

        $config = $serviceLocator->get('Wizard\Config');

        return new WizardResolver($routeMatch, $config);
    }
}
