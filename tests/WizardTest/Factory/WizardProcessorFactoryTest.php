<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\WizardProcessorFactory;
use Wizard\WizardProcessor;
use Zend\Http\Response;
use Zend\Http\Request;

class WizardProcessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardProcessorInstance()
    {
        $requestStub  = $this->getMockBuilder(Request::class)->getMock();
        $responseStub = $this->getMockBuilder(Response::class)->getMock();

        $returnValueMap = [
            ['Request',  $requestStub],
            ['Response', $responseStub],
        ];

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardProcessorFactory();

        $service = $factory($serviceManagerStub);
        $this->assertInstanceOf(WizardProcessor::class, $service);
    }
}
