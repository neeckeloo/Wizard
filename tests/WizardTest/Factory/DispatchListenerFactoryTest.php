<?php
namespace WizardTest\Factory;

use Wizard\Factory\DispatchListenerFactory;
use Zend\ServiceManager\ServiceManager;

class DispatchListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Wizard\WizardFactory',  $this->getWizardFactory())
            ->setService('Wizard\WizardResolver', $this->getWizardResolver());

        $factory = new DispatchListenerFactory();

        $listener = $factory->createService($serviceManager);
        $this->assertInstanceOf('Wizard\Listener\DispatchListener', $listener);
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
}
