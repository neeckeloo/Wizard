<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardResolverFactory;
use Zend\ServiceManager\ServiceManager;

class WizardResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $request = $this->getRequest();
        $router  = $this->getRouter();

        $routeMatch = $this->getRouteMatch();

        $router
            ->expects($this->any())
            ->method('match')
            ->will($this->returnValue($routeMatch));

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Wizard\Config', ['wizard' => []])
            ->setService('Request', $request)
            ->setService('Router', $router);

        $factory = new WizardResolverFactory();

        $resolver = $factory->createService($serviceManager);
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

    private function getRouteMatch()
    {
        return $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
