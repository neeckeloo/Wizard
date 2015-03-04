<?php
namespace WizardTest\Factory;

use Wizard\Factory\IdentifierAccessorFactory;

class IdentifierAccessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateIdentifierAccessorInstance()
    {
        $requestStub = $this->getMock('Zend\Http\Request');

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->with('Request')
            ->will($this->returnValue($requestStub));

        $factory = new IdentifierAccessorFactory();

        $service = $factory->createService($serviceManagerStub);
        $this->assertInstanceOf('Wizard\Wizard\IdentifierAccessor', $service);
    }
}
