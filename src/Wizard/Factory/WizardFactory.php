<?php
namespace Wizard\Factory;

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
        /* @var $wizard \Wizard\WizardInterface */
        $wizard = new Wizard();

        $application = $serviceLocator->get('Application');

        $request = $application->getRequest();
        $wizard->setRequest($request);

        $response = $application->getResponse();
        $wizard->setResponse($response);

        $formFactory = $serviceLocator->get('Wizard\Form\FormFactory');
        $wizard->setFormFactory($formFactory);

        $wizardListener = $serviceLocator->get('Wizard\Listener\WizardListener');
        $wizard->getEventManager()->attachAggregate($wizardListener);

        $stepCollection = $wizard->getSteps();

        $stepCollectionListener = $serviceLocator->get('Wizard\Listener\StepCollectionListener');
        $stepCollection->getEventManager()->attachAggregate($stepCollectionListener);

        return $wizard;
    }
}
