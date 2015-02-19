<?php
namespace WizardTest;

use Wizard\WizardResolver;

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

        $routeMatchMock = $this->getRouteMatch();
        $routeMatchMock
            ->expects($this->once())
            ->method('getMatchedRouteName')
            ->will($this->returnValue($route));

        $resolver = new WizardResolver($routeMatchMock, $config);

        $this->assertEquals($wizard, $resolver->resolve());
    }

    public function testResolverNotMatchWizardRoute()
    {
        $route = 'home/foo';

        $config = [
            'wizards' => [],
        ];

        $routeMatchMock = $this->getRouteMatch();
        $routeMatchMock
            ->expects($this->once())
            ->method('getMatchedRouteName')
            ->will($this->returnValue($route));

        $resolver = new WizardResolver($routeMatchMock, $config);

        $this->assertNull($resolver->resolve());
    }

    private function getRouteMatch()
    {
        return $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
