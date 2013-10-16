<?php
namespace WizardTest;

use Wizard\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    protected $module;

    public function setUp()
    {
        $this->module = new Module();
    }

    public function testGetConfig()
    {
        $config = $this->module->getConfig();
        $this->assertInternalType('array', $config);
    }

    public function testGetAutoloaderConfig()
    {
        $config = $this->module->getAutoloaderConfig();
        $this->assertInternalType('array', $config);
    }
}