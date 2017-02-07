<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\WizardResolverFactory;
use Zend\Router\RouteInterface;
use Zend\Http\Request;
use Wizard\WizardResolver;

class WizardResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardResolverInstance()
    {
        $returnValueMap = [
            ['Wizard\Config', []],
            ['Request',       $this->getRequest()],
            ['Router',        $this->getRouter()],
        ];

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardResolverFactory();

        $resolver = $factory($serviceManagerStub);
        $this->assertInstanceOf(WizardResolver::class, $resolver);
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
