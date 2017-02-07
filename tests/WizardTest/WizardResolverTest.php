<?php
namespace WizardTest;

use Wizard\WizardResolver;
use Zend\Router\RouteInterface;
use Zend\Http\Request;
use Zend\Router\RouteMatch;

class WizardResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolverMatchWizardRoute()
    {
        $wizard = 'foo';
        $route  = 'home/foo';

        $config = [
            'wizards' => [
                $wizard => [
                    'route' => $route,
                ],
            ],
        ];

        $routeMatchStub = $this->getRouteMatch();
        $routeMatchStub
            ->method('getMatchedRouteName')
            ->will($this->returnValue($route));

        $requestStub = $this->getRequest();

        $routerStub = $this->getRouter();
        $routerStub
            ->method('match')
            ->with($requestStub)
            ->will($this->returnValue($routeMatchStub));

        $resolver = new WizardResolver($requestStub, $routerStub, $config);

        $this->assertEquals($wizard, $resolver->resolve());
    }

    public function testResolverNotMatchWizardRoute()
    {
        $route = 'home/foo';

        $config = [
            'wizards' => [],
        ];

        $routeMatchStub = $this->getRouteMatch();
        $routeMatchStub
            ->method('getMatchedRouteName')
            ->will($this->returnValue($route));

        $requestStub = $this->getRequest();

        $routerStub = $this->getRouter();
        $routerStub
            ->method('match')
            ->with($requestStub)
            ->will($this->returnValue($routeMatchStub));

        $resolver = new WizardResolver($requestStub, $routerStub, $config);

        $this->assertNull($resolver->resolve());
    }

    private function getRouteMatch()
    {
        return $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getRequest()
    {
        return $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getRouter()
    {
        return $this->getMockBuilder(RouteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
