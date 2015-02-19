<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardFactoryFactory;
use Zend\ServiceManager\ServiceManager;

class WizardFactoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $stepFactoryMock = $this->getStepFactory();

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Wizard\Config', [])
            ->setService('Wizard\Step\StepFactory', $stepFactoryMock);

        $factory = new WizardFactoryFactory();

        $service = $factory->createService($serviceManager);
        $this->assertInstanceOf('Wizard\WizardFactory', $service);
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder('Wizard\Step\StepFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
