<?php
namespace Wizard\Service;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WizardFactory
     */
    protected $wizardFactory;

    public function setUp()
    {
        $this->wizardFactory = new WizardFactory();
    }

    public function testCreateService()
    {
        $config = array(
            'wizard' => array(),
        );

        $serviceManager = $this->getMock(
            'Zend\ServiceManager\ServiceManager',
            array('get', 'getMvcEvent', 'getRouteMatch'), array(), '', false
        );

        $serviceManager->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($config));

        $routeMatch = $this->getMock('Zend\Mvc\Router\RouteMatch', array(), array(), '', false);

        $serviceManager->expects($this->at(1))
            ->method('get')
            ->will($this->returnSelf());

        $serviceManager->expects($this->once())
            ->method('getMvcEvent')
            ->will($this->returnSelf());

        $serviceManager->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMatch));

        $wizard = $this->wizardFactory->createService($serviceManager);

        $this->assertInstanceOf('Wizard\Wizard', $wizard);
    }
}