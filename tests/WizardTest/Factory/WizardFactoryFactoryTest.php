<?php
namespace WizardTest\Factory;

use Interop\Container\ContainerInterface;
use Wizard\Factory\WizardFactoryFactory;
use Wizard\Step\StepFactory;
use Wizard\WizardFactory;

class WizardFactoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardFactoryInstance()
    {
        $returnValueMap = [
            ['Wizard\Config',           []],
            [StepFactory::class, $this->getStepFactory()],
        ];

        $serviceManagerStub = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardFactoryFactory();

        $service = $factory($serviceManagerStub);
        $this->assertInstanceOf(WizardFactory::class, $service);
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder(StepFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
