<?php
namespace WizardTest\Step;

use Zend\Form\Form;
use Wizard\Step\AbstractStep;
use Wizard\Wizard;
use Wizard\Step\StepOptions;

class AbstractStepTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetOptions()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $this->assertInstanceOf(StepOptions::class, $step->getOptions());

        $options = $this->getMockBuilder(StepOptions::class)
            ->setMockClassName('MockOptions')
            ->getMock();
        $step->setOptions($options);
        $this->assertInstanceOf('MockOptions', $step->getOptions());
    }

    public function testSetAndGetWizard()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $this->assertNull($step->getForm());

        $wizard = $this->getMockBuilder(Wizard::class)
            ->getMock();
        $step->setWizard($wizard);
        $this->assertInstanceOf(Wizard::class, $step->getWizard());
    }

    public function testSetAndGetForm()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $this->assertNull($step->getForm());

        $step->setForm(new Form);
        $this->assertInstanceOf(Form::class, $step->getForm());
    }

    public function testSetAndGetData()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $this->assertCount(0, $step->getData());

        $step->setData([
            'foo' => 123,
            'bar' => 456,
        ]);

        $data = $step->getData();
        $this->assertCount(2, $data);
    }

    public function testSetAndGetComplete()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $this->assertFalse($step->isComplete());

        $step->setComplete(true);
        $this->assertTrue($step->isComplete());

        $step->setComplete(false);
        $this->assertFalse($step->isComplete());

        $step->setComplete();
        $this->assertTrue($step->isComplete());
    }

    public function testSetFromArray()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $step->setFromArray([
            'complete' => true,
            'data' => [
                'foo' => 132,
            ],
        ]);

        $this->assertTrue($step->isComplete());
        $this->assertCount(1, $step->getData());
    }

    public function testToArray()
    {
        $step = $this->getMockForAbstractClass(AbstractStep::class);
        $step->setName('name');

        $options = $step->getOptions();
        $options
            ->setTitle('title')
            ->setViewTemplate('view_template');

        $data = $step->toArray();

        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('name', $data['name']);

        $this->assertArrayHasKey('options', $data);
        $this->assertEquals('title', $data['options']['title']);
        $this->assertEquals('view_template', $data['options']['view_template']);

        $this->assertArrayHasKey('complete', $data);
        $this->assertFalse($data['complete']);

        $this->assertArrayNotHasKey('form', $data);
    }
}
