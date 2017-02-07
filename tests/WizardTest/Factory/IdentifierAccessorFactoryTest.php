<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\IdentifierAccessorFactory;
use Wizard\Wizard\IdentifierAccessor;
use Zend\Http\Request;

class IdentifierAccessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateIdentifierAccessorInstance()
    {
        $requestStub = $this->getMockBuilder(Request::class)
            ->getMock();

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with('Request')
            ->will($this->returnValue($requestStub));

        $factory = new IdentifierAccessorFactory();

        $service = $factory($serviceManagerStub);
        $this->assertInstanceOf(IdentifierAccessor::class, $service);
    }
}
