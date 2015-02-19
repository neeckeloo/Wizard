<?php
namespace WizardTest\Factory;

use Wizard\Factory\ConfigFactory;
use Zend\ServiceManager\ServiceManager;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    public function setUp()
    {
        $this->configFactory = new ConfigFactory();
    }

    public function testCreateConfig()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', [
            'wizard' => [],
        ]);

        $config = $this->configFactory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }
}
