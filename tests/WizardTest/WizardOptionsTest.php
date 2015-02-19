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

    public function testSetAndGetTokenParamName()
    {
        $this->assertEquals('uid', $this->options->getTokenParamName());

        $this->options->setTokenParamName('foo');
        $this->assertEquals('foo', $this->options->getTokenParamName());
    }

    public function testSetAndGetLayoutTemplate()
    {
        $this->assertNull($this->options->getLayoutTemplate());

        $this->options->setLayoutTemplate('foo');
        $this->assertEquals('foo', $this->options->getLayoutTemplate());
    }

    public function testSetAndGetRedirectUrl()
    {
        $this->assertNull($this->options->getRedirectUrl());

        $this->options->setRedirectUrl('/foo');
        $this->assertEquals('/foo', $this->options->getRedirectUrl());
    }

    public function testSetAndGetCancelUrl()
    {
        $this->assertNull($this->options->getCancelUrl());

        $this->options->setCancelUrl('/foo');
        $this->assertEquals('/foo', $this->options->getCancelUrl());
    }
}
