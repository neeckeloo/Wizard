<?php
namespace WizardTest\Factory;

use Wizard\Factory\IdentifierAccessorFactory;

class IdentifierAccessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateConfig()
    {
        $requestMock = $this->getMock('Zend\Http\Request');

        $serviceManagerMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerMock
            ->expects($this->any())
            ->method('get')
            ->with('Request')
            ->will($this->returnValue($requestMock));

        $factory = new IdentifierAccessorFactory();

        $service = $factory->createService($serviceManagerMock);
        $this->assertInstanceOf('Wizard\Wizard\IdentifierAccessor', $service);
    }
}
