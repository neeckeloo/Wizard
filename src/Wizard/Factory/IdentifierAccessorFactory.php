<?php
namespace Wizard\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Wizard\IdentifierAccessor;

class IdentifierAccessorFactory
{
    /**
     * @param  ContainerInterface $container
     * @return IdentifierAccessor
     */
    public function __invoke(ContainerInterface $container)
    {
        $request = $container->get('Request');

        return new IdentifierAccessor($request);
    }
}
