<?php
namespace Wizard\Factory;

use Wizard\Wizard\IdentifierAccessor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IdentifierAccessorFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return IdentifierAccessor
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $request = $serviceLocator->get('Request');

        return new IdentifierAccessor($request);
    }
}
