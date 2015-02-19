<?php
namespace WizardTest\Step;

use Wizard\Step\StepFactory;

class StepFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateStep()
    {
        $stepName    = 'foo';
        $stepOptions = [];

        $stepOptionsMock = $this->getMock('Wizard\Step\StepOptions');

        $stepOptionsMock
            ->expects($this->once())
            ->method('setFromArray')
            ->with($stepOptions);

        $stepOptions['form'] = 'App\Step\FooForm';

        $stepMock = $this->getMock('Wizard\Step\StepInterface');

        $stepMock
            ->expects($this->once())
            ->method('setName')
            ->with($stepName);

        $stepMock
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($stepOptionsMock));

        $stepPluginManagerMock = $this->getStepPluginManager();

        $stepPluginManagerMock
            ->expects($this->once())
            ->method('get')
            ->with($stepName)
            ->will($this->returnValue($stepMock));

        $formElementManagerMock = $this->getFormPluginManager();

        $formElementManagerMock
            ->expects($this->once())
            ->method('get')
            ->with($stepOptions['form'])
            ->will($this->returnValue($this->getMock('Zend\Form\Form')));

        $stepFactory = new StepFactory($stepPluginManagerMock, $formElementManagerMock);

        $step = $stepFactory->create($stepName, $stepOptions);
        $this->assertInstanceOf('Wizard\Step\StepInterface', $step);
    }

    public function getStepPluginManager()
    {
        return $this->getMock('Wizard\Step\StepPluginManager');
    }

    public function getFormPluginManager()
    {
        return $this->getMock('Zend\Form\FormElementManager');
    }
}
