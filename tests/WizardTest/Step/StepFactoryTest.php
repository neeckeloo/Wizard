<?php
namespace WizardTest\Step;

use Wizard\Step\StepFactory;

class StepFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateStepShouldReturnSameInstanceTypeAsReturnedByStepPluginManager()
    {
        $stepStub = $this->getStep();

        $stepPluginManagerStub = $this->getStepPluginManager();
        $stepPluginManagerStub
            ->method('get')
            ->will($this->returnValue($stepStub));

        $formElementManagerDummy = $this->getFormPluginManager();

        $stepFactory = new StepFactory($stepPluginManagerStub, $formElementManagerDummy);

        $step = $stepFactory->create('foo', []);
        $this->assertInstanceOf('Wizard\Step\StepInterface', $step);
    }

    public function testCreateStepShouldSetStepName()
    {
        $stepName = 'foo';

        $stepMock = $this->getStep();
        $stepMock
            ->expects($this->once())
            ->method('setName')
            ->with($stepName);

        $stepPluginManagerStub = $this->getStepPluginManager();
        $stepPluginManagerStub
            ->method('get')
            ->will($this->returnValue($stepMock));

        $formElementManagerDummy = $this->getFormPluginManager();

        $stepFactory = new StepFactory($stepPluginManagerStub, $formElementManagerDummy);

        $stepFactory->create($stepName, []);
    }

    public function testCreateStepWithFormOptionShouldSetFormInstance()
    {
        $stepOptions = ['form' => 'App\Step\FooForm'];

        $formStub = $this->getMock('Zend\Form\Form');

        $stepMock = $this->getStep();
        $stepMock
            ->expects($this->once())
            ->method('setForm')
            ->with($formStub);

        $stepPluginManagerStub = $this->getStepPluginManager();
        $stepPluginManagerStub
            ->method('get')
            ->will($this->returnValue($stepMock));

        $formElementManagerStub = $this->getFormPluginManager();
        $formElementManagerStub
            ->method('get')
            ->with($stepOptions['form'])
            ->will($this->returnValue($formStub));

        $stepFactory = new StepFactory($stepPluginManagerStub, $formElementManagerStub);

        $stepFactory->create('foo', $stepOptions);
    }

    public function getStepPluginManager()
    {
        return $this->getMock('Wizard\Step\StepPluginManager');
    }

    public function getFormPluginManager()
    {
        return $this->getMock('Zend\Form\FormElementManager');
    }

    public function getStep()
    {
        $step = $this->getMock('Wizard\Step\StepInterface');

        $stepOptionsStub = $this->getMock('Wizard\Step\StepOptions');

        $step
            ->method('getOptions')
            ->will($this->returnValue($stepOptionsStub));

        return $step;
    }
}
