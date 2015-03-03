<?php
namespace WizardTest;

use Wizard\Wizard;

class WizardTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetOptions()
    {
        $wizard = new Wizard();
        $this->assertInstanceOf('Wizard\WizardOptions', $wizard->getOptions());

        $options = $this->getMock('Wizard\WizardOptions');
        $wizard->setOptions($options);
        $this->assertInstanceOf('Wizard\WizardOptions', $wizard->getOptions());
    }

    public function testGetCurrentStep()
    {
        $wizard = new Wizard();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $this->assertNull($wizard->getCurrentStep());

        $steps = $wizard->getSteps();
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $steps->add($step);
        }

        $this->assertInstanceOf('Wizard\Step\StepInterface', $wizard->getCurrentStep());
    }

    public function testGetSteps()
    {
        $wizard = new Wizard();
        $this->assertInstanceOf('Wizard\Step\StepCollection', $wizard->getSteps());
    }

    public function testGetFormWithoutSteps()
    {
        $wizard = new Wizard();
        $this->assertNull($wizard->getForm());
    }

    public function testGetFormOfFirstStep()
    {
        $wizard = new Wizard();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $formMock = $this->getMock('Zend\Form\Form');
        $formMock
            ->expects($this->exactly(2))
            ->method('remove')
            ->with($this->logicalOr($this->equalTo('previous'), $this->equalTo('valid')));

        $formFactoryMock = $this->getMock('Wizard\Form\FormFactory');
        $formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryMock);

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);
    }

    public function testGetFormOfMiddleStep()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $formMock = $this->getMock('Zend\Form\Form');
        $formMock
            ->expects($this->once())
            ->method('remove')
            ->with('valid');

        $formFactoryMock = $this->getMock('Wizard\Form\FormFactory');
        $formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryMock);

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));
        $steps->add($this->getStepMock('baz'));

        $form = $wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);
    }

    public function testGetFormOfLastStep()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $formMock = $this->getMock('Zend\Form\Form');
        $formMock
            ->expects($this->once())
            ->method('remove')
            ->with('next');

        $formFactoryMock = $this->getMock('Wizard\Form\FormFactory');
        $formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryMock);

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);
    }

    public function testCurrentStepNumber()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));
        $this->assertEquals(1, $wizard->getCurrentStepNumber());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $this->assertEquals(2, $wizard->getCurrentStepNumber());
    }

    public function testGetTotalStepCount()
    {
        $wizard = new Wizard();

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $this->assertEquals(2, $wizard->getTotalStepCount());
    }

    public function testGetTotalStepCountWithoutSteps()
    {
        $wizard = new Wizard();
        $this->assertEquals(0, $wizard->getTotalStepCount());
    }

    public function testGetPercentProgress()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $this->assertEquals(0, $wizard->getPercentProgress());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $this->assertEquals(50, $wizard->getPercentProgress());
    }

    public function testGetPercentProgressWithoutSteps()
    {
        $wizard = new Wizard();
        $this->assertEquals(0, $wizard->getPercentProgress());
    }

    public function testSetAndGetStepCollection()
    {
        $wizard = new Wizard();
        $this->assertInstanceOf('Wizard\Step\StepCollection', $wizard->getSteps());
    }

    public function testGetCollectionWithRestoredSteps()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $sessionContainer = new \stdClass();
        $sessionContainer->steps = [
            'foo' => [
                'options' => [
                    'title' => 'Foo',
                ],
                'data'  => [
                    'foo' => 123,
                    'bar' => 456,
                ],
            ],
        ];

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $stepCollection = $wizard->getSteps();

        $step = $this->getStepMock('foo');
        $step
            ->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue(new \Zend\Form\Form));
        $stepCollection->add($step);

        $step->getOptions()->setTitle('Foo');

        $this->assertEquals('Foo', $step->getOptions()->getTitle());
        $this->assertInstanceOf('Zend\Form\Form', $step->getForm());
    }

    public function testResetViewModelVariablesWhenChangeCurrentStep()
    {
        $wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $wizard->setIdentifierAccessor($this->getIdentifierAccessorMock());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'foo';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $steps = $wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $viewModel = $wizard->getViewModel();

        $viewModel->setVariable('foo', 123);
        $this->assertInstanceOf('Wizard\Wizard', $viewModel->getVariable('wizard'));
        $this->assertEquals(123, $viewModel->getVariable('foo'));

        $wizard->setCurrentStep('bar');
        $this->assertInstanceOf('Wizard\Wizard', $viewModel->getVariable('wizard'));
        $this->assertNull($viewModel->getVariable('foo', null));
    }

    protected function getStepMock($name)
    {
        $mock = $this->getMockBuilder('Wizard\Step\AbstractStep')
            ->setMethods(['getName', 'getForm', 'isComplete'])
            ->getMock();
        $mock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }

    protected function getIdentifierAccessorMock()
    {
        $mock = $this->getMockBuilder('Wizard\Wizard\IdentifierAccessor')
            ->disableOriginalConstructor()
            ->getMock();
        $mock
            ->expects($this->any())
            ->method('getIdentifier')
            ->will($this->returnValue('foo'));

        return $mock;
    }
}
