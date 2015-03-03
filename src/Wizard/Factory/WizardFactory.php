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

        $formFactory = $serviceLocator->get('Wizard\Form\FormFactory');
        $wizard->setFormFactory($formFactory);

        $wizardProcessor = $serviceLocator->get('Wizard\WizardProcessor');
        $wizard->setWizardProcessor($wizardProcessor);

        $identifierAccessor = $serviceLocator->get('Wizard\Wizard\IdentifierAccessor');
        $wizard->setIdentifierAccessor($identifierAccessor);

        $wizardListener = $serviceLocator->get('Wizard\Listener\WizardListener');
        $wizard->getEventManager()->attachAggregate($wizardListener);

        $stepCollection = $wizard->getSteps();

        $stepCollectionListener = $serviceLocator->get('Wizard\Listener\StepCollectionListener');
        $stepCollection->getEventManager()->attachAggregate($stepCollectionListener);

        return $wizard;
    }
}
