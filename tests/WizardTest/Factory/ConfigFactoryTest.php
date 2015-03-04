<?php
namespace WizardTest\Factory;

use Wizard\Factory\ConfigFactory;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testRetrieveConfigAsArray()
    {
        $config = [
            'wizard' => [],
        ];

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $factory = new ConfigFactory();

        $service = $factory->createService($serviceManagerStub);
        $this->assertInternalType('array', $service);
    }
}
