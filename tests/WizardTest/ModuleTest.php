<?php
namespace WizardTest;

use Wizard\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();
        $this->assertInternalType('array', $config);
    }
}
