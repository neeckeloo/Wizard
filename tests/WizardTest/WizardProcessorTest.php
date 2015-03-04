<?php
namespace WizardTest;

use Wizard\WizardProcessor;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\Parameters;

class WizardProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetStepDataDuringProcess()
    {
        $request = $this->getHttpRequest();

        $params = new Parameters([
            'foo' => 123,
            'bar' => 456,
        ]);
        $request->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardStub = $this->getWizard();
        $wizardProcessor->setWizard($wizardStub);

        $fooStepMock = $wizardStub->getCurrentStep();
        $fooStepMock
            ->method('isComplete')
            ->will($this->returnValue(false));
        $fooStepMock
            ->expects($this->once())
            ->method('process');
        $fooStepMock
            ->expects($this->once())
            ->method('setData')
            ->with($params->toArray());

        $wizardProcessor->process();
    }

    public function testCanGoToPreviousStep()
    {
        $request = $this->getHttpRequest();

        $params = new Parameters(['previous' => true]);
        $request->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizard();
        $wizardProcessor->setWizard($wizardMock);

        $wizardMock
            ->expects($this->once())
            ->method('previousStep');

        $wizardProcessor->process();
    }

    public function testCanGoToNextStep()
    {
        $request  = $this->getHttpRequest();
        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardMock = $this->getWizard();
        $wizardProcessor->setWizard($wizardMock);

        $fooStepStub = $wizardMock->getCurrentStep();
        $fooStepStub
            ->method('isComplete')
            ->will($this->returnValue(false));

        $wizardMock
            ->expects($this->once())
            ->method('nextStep');

        $wizardProcessor->process();
    }

    public function testCanRedirectAfterLastStep()
    {
        $redirectUrl = '/foo';

        $request  = $this->getHttpRequest();
        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardStub = $this->getWizard();
        $wizardProcessor->setWizard($wizardStub);

        $wizardOptionsStub = $wizardStub->getOptions();
        $wizardOptionsStub
            ->method('getRedirectUrl')
            ->will($this->returnValue($redirectUrl));

        $fooStepStub = $wizardStub->getCurrentStep();
        $fooStepStub
            ->method('isComplete')
            ->will($this->returnValue(true));

        $stepCollectionStub = $wizardStub->getSteps();
        $stepCollectionStub
            ->method('isLast')
            ->with($fooStepStub)
            ->will($this->returnValue(true));

        $output = $wizardProcessor->process();

        $this->assertInstanceOf('Zend\Http\Response', $output);
        $this->assertEquals(302, $output->getStatusCode());

        $headers = $output->getHeaders();
        $this->assertEquals($redirectUrl, $headers->get('Location')->getUri());
    }

    public function testCanRedirectAfterCancel()
    {
        $redirectUrl = '/foo';

        $request = $this->getHttpRequest();

        $params = new Parameters(['cancel' => true]);
        $request->setPost($params);

        $response = new HttpResponse;

        $wizardProcessor = new WizardProcessor($request, $response);

        $wizardStub = $this->getWizard();
        $wizardProcessor->setWizard($wizardStub);

        $wizardOptionsStub = $wizardStub->getOptions();
        $wizardOptionsStub
            ->method('getCancelUrl')
            ->will($this->returnValue($redirectUrl));

        $output = $wizardProcessor->process();

        $this->assertInstanceOf('Zend\Http\Response', $output);
        $this->assertEquals(302, $output->getStatusCode());

        $headers = $output->getHeaders();
        $this->assertEquals($redirectUrl, $headers->get('Location')->getUri());
    }

    private function getWizard()
    {
        $wizard = $this->getMock('Wizard\Wizard');

        $wizardOptions = $this->getMock('Wizard\WizardOptions');
        $wizard
            ->method('getOptions')
            ->will($this->returnValue($wizardOptions));

        $eventManager = $this->getEventManager();
        $wizard
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $stepCollection = $this->getMock('Wizard\Step\StepCollection');

        $wizard
            ->method('getSteps')
            ->will($this->returnValue($stepCollection));

        $step = $this->getMock('Wizard\Step\AbstractStep');

        $wizard
            ->method('getCurrentStep')
            ->will($this->returnValue($step));

        return $wizard;
    }

    private function getEventManager()
    {
        return $this->getMock('Zend\EventManager\EventManagerInterface');
    }

    private function getHttpRequest()
    {
        $request = new HttpRequest;
        $request->setMethod(HttpRequest::METHOD_POST);

        return $request;
    }
}
