<?php
namespace Wizard\Step;

use Zend\ServiceManager\AbstractPluginManager;

class StepPluginManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof StepInterface) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Wizard\StepInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}