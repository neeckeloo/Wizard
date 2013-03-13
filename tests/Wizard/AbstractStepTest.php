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
}