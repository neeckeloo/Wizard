<?php
namespace Wizard\Factory;

use Wizard\Listener\DispatchListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DispatchListenerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return DispatchListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resolver = $serviceLocator->get('Wizard\WizardResolver');
        $factory  = $serviceLocator->get('Wizard\WizardFactory');

        return new DispatchListener($resolver, $factory);
    }
}
