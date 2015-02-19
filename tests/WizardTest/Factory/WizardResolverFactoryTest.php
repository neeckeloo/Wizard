<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardResolverFactory;
use Zend\ServiceManager\ServiceManager;

class WizardResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $applicationMock = $this->getApplication();

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Wizard\Config', ['wizard' => []])
            ->setService('Application', $applicationMock);

        $factory = new WizardResolverFactory();

        $resolver = $factory->createService($serviceManager);
        $this->assertInstanceOf('Wizard\WizardResolver', $resolver);
    }

    private function getApplication()
    {
        $routeMatchMock = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();

        $mvcEventMock = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEventMock
            ->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMatchMock));

        $applicationMock = $this->getMockBuilder('Zend\Mvc\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $applicationMock
            ->expects($this->once())
            ->method('getMvcEvent')
            ->will($this->returnValue($mvcEventMock));

        return $applicationMock;
    }
}