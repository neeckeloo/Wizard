<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardResolverFactory;

class WizardResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardResolverInstance()
    {
        $returnValueMap = [
            ['Wizard\Config', []],
            ['Request',       $this->getRequest()],
            ['Router',        $this->getRouter()],
        ];

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardResolverFactory();

        $resolver = $factory->createService($serviceManagerStub);
        $this->assertInstanceOf('Wizard\WizardResolver', $resolver);
    }

    private function getRequest()
    {
        return $this->getMockBuilder('Zend\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getRouter()
    {
        return $this->getMockBuilder('Zend\Mvc\Router\RouteInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
