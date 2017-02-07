<?php
namespace WizardTest;

use Wizard\Wizard;
use Wizard\Wizard\IdentifierAccessor;
use Wizard\Step\AbstractStep;
use Zend\Form\Form;
use Wizard\Step\StepCollection;
use Wizard\Form\FormFactory;
use Wizard\Step\StepInterface;
use Wizard\WizardOptions;

class WizardTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetOptions()
    {
        $wizard = new Wizard();
        $this->assertInstanceOf(WizardOptions::class, $wizard->getOptions());

        $options = $this->getMockBuilder(WizardOptions::class)
            ->getMock();
        $wizard->setOptions($options);
        $this->assertInstanceOf(WizardOptions::class, $wizard->getOptions());
    }

    public function testGetCurrentStep()
    {
        $wizard = new Wizard();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $this->assertNull($wizard->getCurrentStep());

        $steps = $wizard->getSteps();
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStep('step' . $i);
            $steps->add($step);
        }

        $this->assertInstanceOf(StepInterface::class, $wizard->getCurrentStep());
    }

    public function testGetSteps()
    {
        $wizard = new Wizard();
        $this->assertInstanceOf(StepCollection::class, $wizard->getSteps());
    }

    public function testGetFormWithoutStepsShouldNotReturnNull()
    {
        $wizard = new Wizard();
        $this->assertNull($wizard->getForm());
    }

    public function testGetFormWithStepsShouldReturnFormInstance()
    {
        $wizard = new Wizard();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $formStub = $this->getMockBuilder(Form::class)
            ->getMock();

        $formFactoryStub = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryStub
            ->method('create')
            ->will($this->returnValue($formStub));
        $wizard->setFormFactory($formFactoryStub);

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));

        $form = $wizard->getForm();
        $this->assertInstanceOf(Form::class, $form);
    }

    public function testGetFormWhenCurrentStepIsTheFirstShouldRemovePreviousAndValidButton()
    {
        $wizard = new Wizard();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $formMock = $this->getMockBuilder(Form::class)
            ->getMock();
        $formMock
            ->expects($this->exactly(2))
            ->method('remove')
            ->with($this->logicalOr($this->equalTo('previous'), $this->equalTo('valid')));

        $formFactoryStub = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryStub
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryStub);

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));

        $wizard->getForm();
    }

    public function testGetFormWhenCurrentStepIsAtTheMiddleShouldRemoveValidButton()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $formMock = $this->getMockBuilder(Form::class)
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('remove')
            ->with('valid');

        $formFactoryStub = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryStub
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryStub);

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));
        $steps->add($this->getStep('baz'));

        $wizard->getForm();
    }

    public function testGetFormWhenCurrentStepIsTheLastShouldRemoveNextButton()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $formMock = $this->getMockBuilder(Form::class)
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('remove')
            ->with('next');

        $formFactoryStub = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryStub
            ->method('create')
            ->will($this->returnValue($formMock));
        $wizard->setFormFactory($formFactoryStub);

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));

        $wizard->getForm();
    }

    public function testCurrentStepNumber()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));
        $this->assertEquals(1, $wizard->getCurrentStepNumber());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $this->assertEquals(2, $wizard->getCurrentStepNumber());
    }

    public function testGetTotalStepCount()
    {
        $wizard = new Wizard();

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));

        $this->assertEquals(2, $wizard->getTotalStepCount());
    }

    public function testGetTotalStepCountWithoutSteps()
    {
        $wizard = new Wizard();
        $this->assertEquals(0, $wizard->getTotalStepCount());
    }

    public function testGetPercentProgress()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));

        $this->assertEquals(0, $wizard->getPercentProgress());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'bar';

        $wizard
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
        $this->assertInstanceOf(StepCollection::class, $wizard->getSteps());
    }

    public function testGetCollectionWithRestoredSteps()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

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
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $stepCollection = $wizard->getSteps();

        $formStub = $this->getMockBuilder(Form::class)
            ->getMock();

        $step = $this->getStep('foo');
        $step
            ->method('getForm')
            ->will($this->returnValue($formStub));
        $stepCollection->add($step);

        $step->getOptions()->setTitle('Foo');

        $this->assertEquals('Foo', $step->getOptions()->getTitle());
        $this->assertInstanceOf(Form::class, $step->getForm());
    }

    public function testResetViewModelVariablesWhenChangeCurrentStep()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->setMethods(['getSessionContainer'])
            ->getMock();
        $wizard->setIdentifierAccessor($this->getIdentifierAccessor());

        $sessionContainer = new \stdClass();
        $sessionContainer->currentStep = 'foo';

        $wizard
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainer));

        $steps = $wizard->getSteps();
        $steps->add($this->getStep('foo'));
        $steps->add($this->getStep('bar'));

        $viewModel = $wizard->getViewModel();

        $viewModel->setVariable('foo', 123);
        $this->assertInstanceOf(Wizard::class, $viewModel->getVariable('wizard'));
        $this->assertEquals(123, $viewModel->getVariable('foo'));

        $wizard->setCurrentStep('bar');
        $this->assertInstanceOf(Wizard::class, $viewModel->getVariable('wizard'));
        $this->assertNull($viewModel->getVariable('foo', null));
    }

    protected function getStep($name)
    {
        $mock = $this->getMockBuilder(AbstractStep::class)
            ->setMethods(['getName', 'getForm', 'isComplete'])
            ->getMock();
        $mock
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }

    protected function getIdentifierAccessor()
    {
        $mock = $this->getMockBuilder(IdentifierAccessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mock
            ->method('getIdentifier')
            ->will($this->returnValue('foo'));

        return $mock;
    }
}
