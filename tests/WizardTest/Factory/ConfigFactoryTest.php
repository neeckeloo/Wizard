<?php
namespace WizardTest\Factory;

use Wizard\Factory\ConfigFactory;
use Zend\ServiceManager\ServiceManager;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateConfig()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', [
            'wizard' => [],
        ]);

        $factory = new ConfigFactory();

        $config = $factory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }
}
