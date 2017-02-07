<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\WizardProcessor;

class WizardProcessorFactory
{
    /**
     * @param  ContainerInterface $container
     * @return WizardProcessor
     */
    public function __invoke(ContainerInterface $container)
    {
        $request  = $container->get('Request');
        $response = $container->get('Response');

        return new WizardProcessor($request, $response);
    }
}
