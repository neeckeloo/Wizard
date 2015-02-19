<?php
namespace Wizard\Listener;

use Wizard\WizardFactory;
use Wizard\WizardResolver;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Mvc\MvcEvent;

class DispatchListener implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var WizardResolver
     */
    protected $resolver;

    /**
     * @var WizardFactory
     */
    protected $factory;

    /**
     * @param WizardResolver $resolver
     * @param WizardFactory $factory
     */
    public function __construct(WizardResolver $resolver, WizardFactory $factory)
    {
        $this->resolver = $resolver;
        $this->factory  = $factory;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'process'], 10);
    }

    /**
     * @param  MvcEvent $e
     */
    public function process(MvcEvent $e)
    {
        $wizard = $this->resolver->resolve();
        if (!$wizard) {
            return;
        }

        $instance = $this->factory->create($wizard);
        $instance->process();
    }
}
