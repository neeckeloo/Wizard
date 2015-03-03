<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardProcessorFactory;

class WizardProcessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateConfig()
    {
        $requestMock  = $this->getMock('Zend\Http\Request');
        $responseMock = $this->getMock('Zend\Http\Response');

        $returnValueMap = [
            ['Request', $requestMock],
            ['Response', $responseMock],
        ];

        $serviceManagerMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardProcessorFactory();

        $service = $factory->createService($serviceManagerMock);
        $this->assertInstanceOf('Wizard\WizardProcessor', $service);
    }
}
