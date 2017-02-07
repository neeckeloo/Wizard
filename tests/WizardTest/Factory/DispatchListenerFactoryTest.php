<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\DispatchListenerFactory;
use Wizard\WizardFactory;
use Wizard\WizardResolver;
use Wizard\Listener\DispatchListener;

class DispatchListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDispatchListenerInstance()
    {
        $returnValueMap = [
            [WizardFactory::class,  $this->getWizardFactory()],
            [WizardResolver::class, $this->getWizardResolver()],
        ];

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new DispatchListenerFactory();

        $listener = $factory($serviceManagerStub);
        $this->assertInstanceOf(DispatchListener::class, $listener);
    }

    private function getWizardResolver()
    {
        return $this->getMockBuilder(WizardResolver::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizardFactory()
    {
        return $this->getMockBuilder(WizardFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
