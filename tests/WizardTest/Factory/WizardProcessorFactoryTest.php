<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardProcessorFactory;

class WizardProcessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardProcessorInstance()
    {
        $requestStub  = $this->getMock('Zend\Http\Request');
        $responseStub = $this->getMock('Zend\Http\Response');

        $returnValueMap = [
            ['Request',  $requestStub],
            ['Response', $responseStub],
        ];

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardProcessorFactory();

        $service = $factory->createService($serviceManagerStub);
        $this->assertInstanceOf('Wizard\WizardProcessor', $service);
    }
}
