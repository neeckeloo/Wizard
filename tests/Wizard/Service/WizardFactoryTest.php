<?php
namespace Wizard\Service;

use Zend\Mvc\Application;

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
        $application = $this->getApplicationMock();
        $serviceManager = $this->getServiceManagerMock($application);

        $wizard = $this->wizardFactory->createService($serviceManager);

        $this->assertInstanceOf('Wizard\Wizard', $wizard);
    }

    protected function getApplicationMock()
    {
        $methods = array('getRequest', 'getResponse', 'getMvcEvent', 'getRouteMatch');
        $application = $this->getMock('Zend\Mvc\Application', $methods, array(), '', false);

        $request = $this->getMock('Zend\Http\Request');
        $application->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $response = $this->getMock('Zend\Http\Response');
        $application->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $routeMatch = $this->getMock('Zend\Mvc\Router\RouteMatch', array(), array(), '', false);

        $application->expects($this->once())
            ->method('getMvcEvent')
            ->will($this->returnSelf());

        $application->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMatch));

        return $application;
    }

    protected function getServiceManagerMock(Application $application)
    {
        $config = array(
            'wizard' => array(),
            'service_manager' => array(
                'invokables' => array(
                    'session' => 'Zend\Session\Storage\ArrayStorage',
                ),
            ),
        );
        
        $serviceManager = $this->getMock(
            'Zend\ServiceManager\ServiceManager',
            array('get'), array(), '', false
        );

        $serviceManager->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($config));

        $serviceManager->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($application));

        return $serviceManager;
    }
}