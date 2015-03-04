<?php
namespace WizardTest\Step;

use Zend\Form\Form;

class AbstractStepTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetOptions()
    {
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
        $this->assertInstanceOf('Wizard\Step\StepOptions', $step->getOptions());

        $options = $this->getMock('Wizard\Step\StepOptions', [], [], 'MockOptions');
        $step->setOptions($options);
        $this->assertInstanceOf('MockOptions', $step->getOptions());
    }

    public function testSetAndGetWizard()
    {
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
        $this->assertNull($step->getForm());

        $wizard = $this->getMock('Wizard\Wizard');
        $step->setWizard($wizard);
        $this->assertInstanceOf('Wizard\Wizard', $step->getWizard());
    }

    public function testSetAndGetForm()
    {
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
        $this->assertNull($step->getForm());

        $step->setForm(new Form);
        $this->assertInstanceOf('Zend\Form\Form', $step->getForm());
    }

    public function testSetAndGetData()
    {
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
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
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
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
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
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
        $step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
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
