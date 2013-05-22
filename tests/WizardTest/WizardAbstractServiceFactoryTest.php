<?php
namespace WizardTest;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class WizardAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    protected function setUp()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(array(
            'abstract_factories' => array('Wizard\WizardAbstractServiceFactory'),
        )));

        $this->serviceManager->setService('Config', array(
            'wizard' => array(
                'default_class' => 'Wizard\Wizard',
                'default_layout_template' => 'wizard/layout',
                'wizards' => array(
                    'Wizard\Foo' => array(),
                    'Wizard\Bar' => array(),
                ),
            ),
        ));

        $application = $this->getMock('\Zend\Mvc\Application', array(), array(), '', false);
        $application
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Zend\Http\Request')));
        $application
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->getMock('Zend\Http\Response')));
        $this->serviceManager->setService('Application', $application);

        $sessionManager = $this->getMock('Zend\Session\SessionManager');
        $this->serviceManager->setService('Session\Manager', $sessionManager);

        $renderer = $this->getMock('Zend\View\Renderer\PhpRenderer');
        $this->serviceManager->setService('Wizard\WizardRenderer', $renderer);
    }

    /**
     * @return array
     */
    public function providerValidWizardService()
    {
        return array(
            array('Wizard\Foo'),
            array('Wizard\Bar'),
        );
    }

    /**
     * @return array
     */
    public function providerInvalidWizardService()
    {
        return array(
            array('Wizard\Application\Unknown'),
            array('Wizard\Application\Frontend'),
            array('Application\Backend\Wizard'),
        );
    }

    /**
     * @param string $service
     * @dataProvider providerValidWizardService
     */
    public function testValidWizardService($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Wizard\Wizard', $actual);
    }

    /**
     * @param string $service
     * @dataProvider providerInvalidWizardService
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testInvalidWizardService($service)
    {
        $actual = $this->serviceManager->get($service);
    }
}