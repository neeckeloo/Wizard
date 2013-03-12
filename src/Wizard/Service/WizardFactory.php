<?php
namespace Wizard\Service;

use Wizard\Wizard;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Wizard
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        //$configuration = new Configuration($config['wizard']);

        $routeMatch = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch();

        return new Wizard($routeMatch);
    }
}