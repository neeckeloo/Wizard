<?php
namespace WizardTest;

use Wizard\WizardOptions;

class WizardOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetTokenParamName()
    {
        $options = new WizardOptions();
        $this->assertEquals('uid', $options->getTokenParamName());

        $options->setTokenParamName('foo');
        $this->assertEquals('foo', $options->getTokenParamName());
    }

    public function testSetAndGetLayoutTemplate()
    {
        $options = new WizardOptions();
        $this->assertNull($options->getLayoutTemplate());

        $options->setLayoutTemplate('foo');
        $this->assertEquals('foo', $options->getLayoutTemplate());
    }

    public function testSetAndGetRedirectUrl()
    {
        $options = new WizardOptions();
        $this->assertNull($options->getRedirectUrl());

        $options->setRedirectUrl('/foo');
        $this->assertEquals('/foo', $options->getRedirectUrl());
    }

    public function testSetAndGetCancelUrl()
    {
        $options = new WizardOptions();
        $this->assertNull($options->getCancelUrl());

        $options->setCancelUrl('/foo');
        $this->assertEquals('/foo', $options->getCancelUrl());
    }
}
