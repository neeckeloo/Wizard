<?php
namespace WizardTest\Factory;

use Wizard\Factory\WizardFactoryFactory;

class WizardFactoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizardFactoryInstance()
    {
        $returnValueMap = [
            ['Wizard\Config',           []],
            ['Wizard\Step\StepFactory', $this->getStepFactory()],
        ];

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $factory = new WizardFactoryFactory();

        $service = $factory->createService($serviceManagerStub);
        $this->assertInstanceOf('Wizard\WizardFactory', $service);
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder('Wizard\Step\StepFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
