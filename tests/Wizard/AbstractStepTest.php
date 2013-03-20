<?php
namespace Wizard;

use Zend\Form\Form;

class AbstractStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractStep
     */
    protected $step;

    public function setUp()
    {
        $this->step = $this->getMockForAbstractClass('Wizard\AbstractStep');
    }

    public function testSetAndGetTitle()
    {
        $this->assertNull($this->step->getTitle());

        $this->step->setTitle('foo');
        $this->assertEquals('foo', $this->step->getTitle());
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
        $this->step
            ->setTitle('foo');

        $options = $this->step->toArray();

        $this->assertArrayHasKey('title', $options);
        $this->assertEquals('foo', $options['title']);

        $this->assertArrayHasKey('complete', $options);
        $this->assertFalse($options['complete']);

        $this->assertArrayNotHasKey('form', $options);
    }
}