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

        $this->serviceManager->setService('Wizard\Config', array(
            'default_layout_template' => 'wizard/layout',
            'wizards' => array(
                'Wizard\Foo' => array(),
                'Wizard\Bar' => array(),
            ),
        ));

        $wizard = $this->getMock('Wizard\Wizard');

        $wizardFactory = $this->getMockBuilder('Wizard\WizardFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $wizardFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($wizard));

        $this->serviceManager->setService('Wizard\Factory', $wizardFactory);
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
     * @param string $service
     * @dataProvider providerValidWizardService
     */
    public function testValidWizardService($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Wizard\Wizard', $actual);
    }
}