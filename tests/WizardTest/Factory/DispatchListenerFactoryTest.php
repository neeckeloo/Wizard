<?php
namespace WizardTest\Factory;

use Wizard\Factory\DispatchListenerFactory;

class DispatchListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDispatchListenerInstance()
    {
        $returnValueMap = [
            ['Wizard\WizardFactory',  $this->getWizardFactory()],
            ['Wizard\WizardResolver', $this->getWizardResolver()],
        ];

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new DispatchListenerFactory();

        $listener = $factory->createService($serviceManagerStub);
        $this->assertInstanceOf('Wizard\Listener\DispatchListener', $listener);
    }

    private function getWizardResolver()
    {
        return $this->getMockBuilder('Wizard\WizardResolver')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizardFactory()
    {
        return $this->getMockBuilder('Wizard\WizardFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
