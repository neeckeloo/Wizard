<?php
namespace WizardTest;

use Wizard\WizardProcessor;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

class WizardProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetStepDataDuringProcess()
    {
        $data = [
            'foo' => 123,
            'bar' => 456,
        ];
        $params = new \Zend\Stdlib\Parameters($data);

        $request = new HttpRequest;
        $request
            ->setMethod(HttpRequest::METHOD_POST)
            ->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizardMock();
        $wizardProcessor->setWizard($wizardMock);

        $fooStepMock = $this->getMock('Wizard\Step\AbstractStep');
        $fooStepMock
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(false));
        $fooStepMock
            ->expects($this->once())
            ->method('process');
        $fooStepMock
            ->expects($this->once())
            ->method('setData')
            ->with($data);

        $wizardMock
            ->expects($this->any())
            ->method('getCurrentStep')
            ->will($this->returnValue($fooStepMock));

        $stepCollectionMock = $this->getMock('Wizard\Step\StepCollection');

        $wizardMock
            ->expects($this->any())
            ->method('getSteps')
            ->will($this->returnValue($stepCollectionMock));

        $wizardProcessor->process();
    }

    public function testCanGoToPreviousStep()
    {
        $data = ['previous' => true];
        $params = new \Zend\Stdlib\Parameters($data);

        $request = new HttpRequest;
        $request
            ->setMethod(HttpRequest::METHOD_POST)
            ->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizardMock();
        $wizardProcessor->setWizard($wizardMock);

        $wizardMock
            ->expects($this->once())
            ->method('previousStep');

        $wizardProcessor->process();
    }

    public function testCanGoToNextStep()
    {
        $request = new HttpRequest;
        $request->setMethod(HttpRequest::METHOD_POST);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizardMock();
        $wizardProcessor->setWizard($wizardMock);

        $fooStepMock = $this->getMock('Wizard\Step\AbstractStep');
        $fooStepMock
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(false));

        $wizardMock
            ->expects($this->any())
            ->method('getCurrentStep')
            ->will($this->returnValue($fooStepMock));

        $wizardMock
            ->expects($this->once())
            ->method('nextStep');

        $wizardProcessor->process();
    }

    public function testCanRedirectAfterLastStep()
    {
        $redirectUrl = '/foo';

        $request = new HttpRequest;
        $request->setMethod(HttpRequest::METHOD_POST);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizardMock();
        $wizardProcessor->setWizard($wizardMock);

        $wizardOptionsMock = $this->getMock('Wizard\WizardOptions');
        $wizardOptionsMock
            ->expects($this->any())
            ->method('getRedirectUrl')
            ->will($this->returnValue($redirectUrl));

        $wizardMock
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($wizardOptionsMock));

        $fooStepMock = $this->getMock('Wizard\Step\AbstractStep');
        $fooStepMock
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(true));

        $wizardMock
            ->expects($this->any())
            ->method('getCurrentStep')
            ->will($this->returnValue($fooStepMock));

        $stepCollectionMock = $this->getMock('Wizard\Step\StepCollection');

        $stepCollectionMock
            ->expects($this->any())
            ->method('isLast')
            ->with($fooStepMock)
            ->will($this->returnValue(true));

        $wizardMock
            ->expects($this->any())
            ->method('getSteps')
            ->will($this->returnValue($stepCollectionMock));

        $output = $wizardProcessor->process();

        $this->assertInstanceOf('Zend\Http\Response', $output);
        $this->assertEquals(302, $output->getStatusCode());

        $headers = $output->getHeaders();
        $this->assertEquals($redirectUrl, $headers->get('Location')->getUri());
    }

    public function testCanRedirectAfterCancel()
    {
        $redirectUrl = '/foo';

        $data = ['cancel' => true];
        $params = new \Zend\Stdlib\Parameters($data);

        $request = new HttpRequest;
        $request
            ->setMethod(HttpRequest::METHOD_POST)
            ->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizardMock();
        $wizardProcessor->setWizard($wizardMock);

        $wizardOptionsMock = $this->getMock('Wizard\WizardOptions');
        $wizardOptionsMock
            ->expects($this->any())
            ->method('getCancelUrl')
            ->will($this->returnValue($redirectUrl));

        $wizardMock
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($wizardOptionsMock));

        $output = $wizardProcessor->process();

        $this->assertInstanceOf('Zend\Http\Response', $output);
        $this->assertEquals(302, $output->getStatusCode());

        $headers = $output->getHeaders();
        $this->assertEquals($redirectUrl, $headers->get('Location')->getUri());
    }

    private function getWizardMock()
    {
        $wizardMock = $this->getMock('Wizard\Wizard');

        $eventManagerMock = $this->getEventManagerMock();
        $wizardMock
            ->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManagerMock));

        return $wizardMock;
    }

    private function getEventManagerMock()
    {
        $eventManagerMock = $this->getMock('Zend\EventManager\EventManagerInterface');
        $eventManagerMock
            ->expects($this->any())
            ->method('trigger');

        return $eventManagerMock;
    }
}
