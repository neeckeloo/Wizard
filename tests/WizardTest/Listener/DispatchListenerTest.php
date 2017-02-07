<?php
namespace WizardTest\Listener;

use Wizard\Listener\DispatchListener;
use Zend\Mvc\MvcEvent;
use Wizard\Wizard;
use Wizard\WizardFactory;
use Wizard\WizardResolver;

class DispatchListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWizardWithSuccessfulResolving()
    {
        $wizard = 'foo';

        $wizardMock = $this->getWizard();
        $wizardMock
            ->expects($this->once())
            ->method('process');

        $wizardResolverStub = $this->getWizardResolver();
        $wizardResolverStub
            ->method('resolve')
            ->will($this->returnValue($wizard));

        $wizardFactoryStub = $this->getWizardFactory();
        $wizardFactoryStub
            ->method('create')
            ->with($wizard)
            ->will($this->returnValue($wizardMock));

        $listener = new DispatchListener($wizardResolverStub, $wizardFactoryStub);
        $event    = new MvcEvent();

        $listener->process($event);
    }

    public function testProcessWizardWithFailedResolving()
    {
        $wizardMock = $this->getWizard();
        $wizardMock
            ->expects($this->never())
            ->method('process');

        $wizardResolverStub = $this->getWizardResolver();
        $wizardResolverStub
            ->method('resolve')
            ->will($this->returnValue(null));

        $wizardFactoryStub = $this->getWizardFactory();

        $listener = new DispatchListener($wizardResolverStub, $wizardFactoryStub);
        $event    = new MvcEvent();

        $listener->process($event);
    }

    private function getWizardResolver()
    {
        return $this->getMockBuilder(WizardResolver::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizardFactory()
    {
        return $this->getMockBuilder(WizardFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizard()
    {
        return $this->getMockBuilder(Wizard::class)
            ->getMock();
    }
}
