<?php
namespace Wizard;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $services->get('Wizard\Config');
        return isset($config['wizards'][$requestedName]);
    }

    /**
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return Wizard
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $wizardFactory = $services->get('Wizard\Factory');
        return $wizardFactory->create($requestedName);
    }
}