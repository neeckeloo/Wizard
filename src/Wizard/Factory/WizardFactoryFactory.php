<?php
namespace Wizard\Factory;

use Wizard\WizardFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactoryFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return WizardFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Wizard\Config');
        $factory = new WizardFactory($config);

        $application = $serviceLocator->get('Application');

        $request = $application->getRequest();
        $factory->setRequest($request);

        $response = $application->getResponse();
        $factory->setResponse($response);

        $formFactory = $serviceLocator->get('Wizard\Form\FormFactory');
        $factory->setFormFactory($formFactory);

        return $factory;
    }
}