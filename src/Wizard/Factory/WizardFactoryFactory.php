<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\WizardFactory;
use Wizard\Step\StepFactory;

class WizardFactoryFactory
{
    /**
     * @param  ContainerInterface $container
     * @return WizardFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('Wizard\Config');

        $wizardFactory = new WizardFactory($config);
        $wizardFactory->setServiceManager($container);

        $stepFactory = $container->get(StepFactory::class);
        $wizardFactory->setStepFactory($stepFactory);

        return $wizardFactory;
    }
}
