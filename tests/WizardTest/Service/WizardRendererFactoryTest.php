<?php
namespace WizardTest\Service;

use Wizard\Service\WizardRendererFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardRendererFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var WizardRendererFactory
     */
    protected $wizardRendererFactory;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->wizardRendererFactory = new WizardRendererFactory();
    }

    public function testCreateRenderer()
    {
        $resolver = $this->getMock('Zend\View\Resolver\ResolverInterface');

        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($resolver));

        $renderer = $this->wizardRendererFactory->createService($this->serviceLocator);
        $this->assertInstanceOf('Zend\View\Renderer\PhpRenderer', $renderer);
    }
}