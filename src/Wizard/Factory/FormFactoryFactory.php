<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Form\FormFactory;

class FormFactoryFactory
{

    /**
     * @param ContainerInterface $container
     * @return FormFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        return new FormFactory($container);
    }
}
