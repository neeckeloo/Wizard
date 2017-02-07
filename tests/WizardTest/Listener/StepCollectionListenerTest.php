<?php
namespace WizardTest\Listener;

use Wizard\Listener\StepCollectionListener;
use Zend\Mvc\MvcEvent;
use Wizard\WizardInterface;
use Wizard\Step\StepInterface;

class StepCollectionListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRestoreStep()
    {
        $listener = new StepCollectionListener();
        $event    = new MvcEvent();

        $wizardStub = $this->getWizard();

        $sessionContainer = $wizardStub->getSessionContainer();
        $sessionContainer->steps = [
            'foo' => [],
        ];

        $stepMock = $this->getMockBuilder(StepInterface::class)
            ->getMock();
        $stepMock
            ->method('getName')
            ->will($this->returnValue('foo'));
        $stepMock
            ->method('getWizard')
            ->will($this->returnValue($wizardStub));
        $stepMock
            ->expects($this->once())
            ->method('setFromArray');

        $event->setTarget($stepMock);

        $listener->restore($event);
    }

    public function testNotRestoreWithoutSessionSteps()
    {
        $listener = new StepCollectionListener();
        $event    = new MvcEvent();

        $stepMock = $this->getMockBuilder(StepInterface::class)
            ->getMock();
        $stepMock
            ->method('getWizard')
            ->will($this->returnValue($this->getWizard()));
        $stepMock
            ->expects($this->never())
            ->method('setFromArray');

        $event->setTarget($stepMock);

        $listener->restore($event);
    }

    private function getWizard()
    {
        $sessionContainerFake = new \stdClass();

        $wizardMock = $this->getMockBuilder(WizardInterface::class)
            ->getMock();
        $wizardMock
            ->method('getSessionContainer')
            ->will($this->returnValue($sessionContainerFake));

        return $wizardMock;
    }
}
