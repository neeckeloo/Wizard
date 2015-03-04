<?php
namespace WizardTest\Step;

use Wizard\Step\StepOptions;

class StepOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetTitle()
    {
        $options = new StepOptions();
        $this->assertNull($options->getTitle());

        $options->setTitle('foo');
        $this->assertEquals('foo', $options->getTitle());
    }

    public function testSetAndGetViewTemplate()
    {
        $options = new StepOptions();
        $this->assertNull($options->getViewTemplate());

        $options->setViewTemplate('foo');
        $this->assertEquals('foo', $options->getViewTemplate());
    }
}
