<?php
namespace WizardTest;

use Wizard\Step\StepOptions;

class StepOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StepOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new StepOptions();
    }

    public function testSetAndGetTitle()
    {
        $this->assertNull($this->options->getTitle());

        $this->options->setTitle('foo');
        $this->assertEquals('foo', $this->options->getTitle());
    }

    public function testSetAndGetViewTemplate()
    {
        $this->assertNull($this->options->getViewTemplate());

        $this->options->setViewTemplate('foo');
        $this->assertEquals('foo', $this->options->getViewTemplate());
    }
}