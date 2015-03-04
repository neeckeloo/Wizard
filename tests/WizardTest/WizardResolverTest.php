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

        $routeMatchStub = $this->getRouteMatch();
        $routeMatchStub
            ->method('getMatchedRouteName')
            ->will($this->returnValue($route));

        $resolver = new WizardResolver($routeMatchStub, $config);

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

        $resolver = new WizardResolver($routeMatchStub, $config);

        $this->assertNull($resolver->resolve());
    }

    private function getRouteMatch()
    {
        return $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
