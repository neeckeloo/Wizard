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
            array('get'), array(), '', false
        );

        $serviceManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $wizard = $this->wizardFactory->createService($serviceManager);

        $this->assertInstanceOf('Wizard\Wizard', $wizard);
    }
}