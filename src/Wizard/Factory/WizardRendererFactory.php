<?php
namespace Wizard\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\PhpRenderer;

class WizardRendererFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return PhpRenderer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $renderer = new PhpRenderer;

        $resolver = $serviceLocator->get('ViewResolver');
        $renderer->setResolver($resolver);

        return $renderer;
    }
}