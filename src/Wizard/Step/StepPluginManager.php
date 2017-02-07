<?php
namespace Wizard\Step;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

class StepPluginManager extends AbstractPluginManager
{
    protected $instanceOf = StepInterface::class;

    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($instance),
                $this->instanceOf
            ));
        }
    }
}