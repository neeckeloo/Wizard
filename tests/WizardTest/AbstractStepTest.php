<?php
namespace WizardTest;

use Wizard\Step\AbstractStep;
use Zend\Form\Form;

class AbstractStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractStep
     */
    protected $step;

    public function setUp()
    {
        $this->step = $this->getMockForAbstractClass('Wizard\Step\AbstractStep');
    }

    public function testSetAndGetOptions()
    {
        $this->assertInstanceOf('Wizard\Step\StepOptions', $this->step->getOptions());

        $options = $this->getMock('Wizard\Step\StepOptions', array(), array(), 'MockOptions');
        $this->step->setOptions($options);
        $this->assertInstanceOf('MockOptions', $this->step->getOptions());
    }

    public function testSetAndGetWizard()
    {
        $this->assertNull($this->step->getForm());

        $wizard = $this->getMock('Wizard\Wizard');
        $this->step->setWizard($wizard);
        $this->assertInstanceOf('Wizard\Wizard', $this->step->getWizard());
    }

    public function testSetAndGetForm()
    {
        $this->assertNull($this->step->getForm());

        $this->step->setForm(new Form);
        $this->assertInstanceOf('Zend\Form\Form', $this->step->getForm());
    }

    public function testSetAndGetData()
    {
        $this->assertCount(0, $this->step->getData());

        $this->step->setData(array(
            'foo' => 123,
            'bar' => 456,
        ));

        $data = $this->step->getData();
        $this->assertCount(2, $data);
    }

    public function testSetAndGetComplete()
    {
        $this->assertFalse($this->step->isComplete());

        $this->step->setComplete(true);
        $this->assertTrue($this->step->isComplete());

        $this->step->setComplete(false);
        $this->assertFalse($this->step->isComplete());

        $this->step->setComplete();
        $this->assertTrue($this->step->isComplete());
    }

    public function testSetFromArray()
    {
        $this->step->setFromArray(array(
            'complete' => true,
            'data'        => array(
                'foo' => 132,
            ),
        ));

        $this->assertTrue($this->step->isComplete());
        $this->assertCount(1, $this->step->getData());
    }

    public function testToArray()
    {
        $this->step->setName('name');

        $options = $this->step->getOptions();
        $options
            ->setTitle('title')
            ->setViewTemplate('view_template');

        $data = $this->step->toArray();

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