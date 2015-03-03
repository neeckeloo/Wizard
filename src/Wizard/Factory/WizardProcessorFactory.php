<?php
namespace Wizard\Factory;

use Wizard\WizardProcessor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardProcessorFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return WizardProcessor
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $request  = $serviceLocator->get('Request');
        $response = $serviceLocator->get('Response');

        return new WizardProcessor($request, $response);
    }
}
