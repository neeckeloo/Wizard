<?php
namespace WizardTest\Listener;

use Wizard\Listener\DispatchListener;
use Zend\Mvc\MvcEvent;

class DispatchListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWizardWithSuccessfulResolving()
    {
        $wizard = 'foo';

        $wizardMock = $this->getWizard();
        $wizardMock
            ->expects($this->once())
            ->method('process');

        $wizardResolverMock = $this->getWizardResolver();
        $wizardResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($wizard));

        $wizardFactoryMock = $this->getWizardFactory();
        $wizardFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($wizard)
            ->will($this->returnValue($wizardMock));

        $listener = new DispatchListener($wizardResolverMock, $wizardFactoryMock);
        $event    = new MvcEvent();

        $listener->process($event);
    }

    public function testProcessWizardWithFailedResolving()
    {
        $wizardMock = $this->getWizard();
        $wizardMock
            ->expects($this->never())
            ->method('process');

        $wizardResolverMock = $this->getWizardResolver();
        $wizardResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(null));

        $wizardFactoryMock = $this->getWizardFactory();
        $wizardFactoryMock
            ->expects($this->never())
            ->method('create');

        $listener = new DispatchListener($wizardResolverMock, $wizardFactoryMock);
        $event    = new MvcEvent();

        $listener->process($event);
    }

    private function getWizardResolver()
    {
        return $this->getMockBuilder('Wizard\WizardResolver')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizardFactory()
    {
        return $this->getMockBuilder('Wizard\WizardFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWizard()
    {
        return $this->getMock('Wizard\Wizard');
    }
}
