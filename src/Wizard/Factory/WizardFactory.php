<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Step\StepCollection;
use Wizard\Wizard;
use Wizard\Form\FormFactory;
use Wizard\WizardProcessor;
use Wizard\Wizard\IdentifierAccessor;
use Wizard\Listener\WizardListener;
use Wizard\Listener\StepCollectionListener;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class WizardFactory
{
    /**
     * @param  ContainerInterface $container
     * @return Wizard
     */
    public function __invoke(ContainerInterface $container)
    {
        /* @var $wizard \Wizard\WizardInterface */
        $wizard = new Wizard();

        $formFactory = $container->get(FormFactory::class);
        $wizard->setEventManager($this->createEventManager($container));
        $wizard->setFormFactory($formFactory);

        $wizardProcessor = $container->get(WizardProcessor::class);
        $wizard->setWizardProcessor($wizardProcessor);

        $identifierAccessor = $container->get(IdentifierAccessor::class);
        $wizard->setIdentifierAccessor($identifierAccessor);

        $wizardListener = $container->get(WizardListener::class);
        $wizardListener->attach($wizard->getEventManager());

        $stepCollection = new StepCollection();
        $stepCollection->setEventManager($this->createEventManager($container));
        $wizard->setSteps($stepCollection);

        $stepCollectionListener = $container->get(StepCollectionListener::class);
        $stepCollectionListener->attach($stepCollection->getEventManager());

        return $wizard;
    }

    /**
     * @param ContainerInterface $container
     * @return EventManagerInterface
     */
    protected function createEventManager(ContainerInterface $container)
    {
        return $container->has('EventManager')
            ? $container->get('EventManager')
            : new EventManager(
                $container->has('SharedEventManager')
                    ? $container->get('SharedEventManager')
                    : null
            );
    }
}
