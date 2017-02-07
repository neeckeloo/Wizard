<?php
namespace WizardTest\Step;

use Wizard\Step\StepFactory;
use Wizard\Step\StepOptions;
use Wizard\Step\StepInterface;
use Zend\Form\FormElementManager;
use Wizard\Step\StepPluginManager;
use Zend\Form\Form;

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
        $this->assertInstanceOf(StepInterface::class, $step);
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

        $formStub = $this->getMockBuilder(Form::class)
            ->getMock();

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
        return $this->getMockBuilder(StepPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getFormPluginManager()
    {
        return $this->getMockBuilder(FormElementManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getStep()
    {
        $step = $this->getMockBuilder(StepInterface::class)
            ->getMock();

        $stepOptionsStub = $this->getMockBuilder(StepOptions::class)
            ->getMock();

        $step
            ->method('getOptions')
            ->will($this->returnValue($stepOptionsStub));

        return $step;
    }
}
