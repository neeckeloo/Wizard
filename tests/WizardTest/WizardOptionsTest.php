<?php
namespace WizardTest;

use Wizard\WizardOptions;

class WizardOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WizardOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new WizardOptions();
    }

    public function testSetAndGetRedirectUrl()
    {
        $this->assertNull($this->options->getRedirectUrl());

        $this->options->setRedirectUrl('/foo');
        $this->assertEquals('/foo', $this->options->getRedirectUrl());
    }

    public function testSetAndGetLayoutTemplate()
    {
        $this->assertNull($this->options->getLayoutTemplate());

        $this->options->setLayoutTemplate('foo');
        $this->assertEquals('foo', $this->options->getLayoutTemplate());
    }
}