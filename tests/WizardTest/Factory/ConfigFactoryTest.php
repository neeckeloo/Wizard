<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\ConfigFactory;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testRetrieveConfigAsArray()
    {
        $config = [
            'wizard' => [],
        ];

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with('config')
            ->will($this->returnValue($config));

        $factory = new ConfigFactory();

        $service = $factory($serviceManagerStub);
        $this->assertInternalType('array', $service);
    }
}
