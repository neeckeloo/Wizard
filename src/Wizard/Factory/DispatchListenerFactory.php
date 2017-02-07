<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Listener\DispatchListener;
use Wizard\WizardResolver;
use Wizard\WizardFactory;

class DispatchListenerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return DispatchListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $resolver = $container->get(WizardResolver::class);
        $factory  = $container->get(WizardFactory::class);

        return new DispatchListener($resolver, $factory);
    }
}
