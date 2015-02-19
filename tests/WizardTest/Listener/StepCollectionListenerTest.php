<?php
namespace WizardTest\Listener;

use Wizard\Listener\StepCollectionListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage as SessionStorage;

class StepCollectionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StepListener
     */
    protected $listener;

    /**
     * @var MvcEvent
     */
    protected $event;

    public function setUp()
    {
        $this->sessionContainer = $this->getSessionContainer();

        $this->listener = new StepCollectionListener($this->sessionContainer);
        $this->event    = new MvcEvent();
    }

    public function testRestoreStep()
    {
        $this->sessionContainer->steps = [
            'foo' => [],
        ];

        $wizard = $this->getMock('Wizard\WizardInterface');
        $wizard
            ->expects($this->once())
            ->method('getSessionContainer')
            ->will($this->returnValue($this->sessionContainer));

        $step = $this->getMock('Wizard\Step\StepInterface');
        $step
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $step
            ->expects($this->once())
            ->method('getWizard')
            ->will($this->returnValue($wizard));
        $step
            ->expects($this->once())
            ->method('setFromArray');

        $this->event
            ->setTarget($step);

        $this->listener->restore($this->event);
    }

    public function testNotRestoreWithoutSessionSteps()
    {
        $wizard = $this->getMock('Wizard\WizardInterface');
        $wizard
            ->expects($this->once())
            ->method('getSessionContainer')
            ->will($this->returnValue($this->sessionContainer));

        $step = $this->getMock('Wizard\Step\StepInterface');
        $step
            ->expects($this->never())
            ->method('getName');
        $step
            ->expects($this->once())
            ->method('getWizard')
            ->will($this->returnValue($wizard));
        $step
            ->expects($this->never())
            ->method('setFromArray');

        $this->event
            ->setTarget($step);

        $this->listener->restore($this->event);
    }

    /**
     * @return SessionContainer
     */
    public function getSessionContainer()
    {
        $sessionStorage = new SessionStorage;
        $sessionManager = new SessionManager(null, $sessionStorage);

        return new SessionContainer('foo', $sessionManager);
    }
}
