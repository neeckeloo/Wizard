<?php
namespace WizardTest\Factory;

use Wizard\Factory\ConfigFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->configFactory = new ConfigFactory();
    }

    public function testCreateConfig()
    {
        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(array(
                'wizard' => array(),
            )));

        $config = $this->configFactory->createService($this->serviceLocator);
        $this->assertInternalType('array', $config);
    }
}