<?php
namespace WizardTest;

use Wizard\Wizard;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Session\Container as SessionContainer;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class WizardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Wizard
     */
    protected $wizard;

    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function setUp()
    {
        $this->request = new Request;
        $this->response = new Response;

        $this->wizard = $this->getMock('Wizard\Wizard', ['getSessionContainer']);
        $this->wizard
            ->setRequest($this->request)
            ->setResponse($this->response);

        $this->sessionContainer = new SessionContainer('foo');
        $this->sessionContainer->getManager()->getStorage()->clear('foo');

        $this->wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($this->sessionContainer));
    }

    /**
     * @return FormFactory
     */
    protected function getFormFactory()
    {
        $formFactory = $this->getMock('Wizard\Form\FormFactory');

        $form = new Form();

        $buttons = [
            'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Next',
            'Wizard\Form\Element\Button\Valid',
            'Wizard\Form\Element\Button\Cancel',
        ];
        foreach ($buttons as $class) {
            $button = new $class();
            $form->add($button);
        }

        $formFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($form));

        return $formFactory;
    }

    public function testSetAndGetOptions()
    {
        $this->assertInstanceOf('Wizard\WizardOptions', $this->wizard->getOptions());

        $options = $this->getMock('Wizard\WizardOptions');
        $this->wizard->setOptions($options);
        $this->assertInstanceOf('Wizard\WizardOptions', $this->wizard->getOptions());
    }

    public function testGetCurrentStep()
    {
        $this->assertNull($this->wizard->getCurrentStep());

        $steps = $this->wizard->getSteps();
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $steps->add($step);
        }

        $this->assertInstanceOf('Wizard\Step\StepInterface', $this->wizard->getCurrentStep());
    }

    public function testGetSteps()
    {
        $this->assertInstanceOf('Wizard\Step\StepCollection', $this->wizard->getSteps());
    }

    public function testGetFormWithoutSteps()
    {
        $this->assertNull($this->wizard->getForm());
    }

    public function testGetFormOfFirstStep()
    {
        $formFactory = $this->getFormFactory();
        $this->wizard->setFormFactory($formFactory);

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertFalse($form->has('previous'));
        $this->assertTrue($form->has('next'));
        $this->assertFalse($form->has('valid'));
        $this->assertTrue($form->has('cancel'));
    }

    public function testGetFormOfMiddleStep()
    {
        $formFactory = $this->getFormFactory();
        $this->wizard->setFormFactory($formFactory);

        $this->sessionContainer->currentStep = 'bar';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));
        $steps->add($this->getStepMock('baz'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertTrue($form->has('previous'));
        $this->assertTrue($form->has('next'));
        $this->assertFalse($form->has('valid'));
        $this->assertTrue($form->has('cancel'));
    }

    public function testGetFormOfLastStep()
    {
        $formFactory = $this->getFormFactory();
        $this->wizard->setFormFactory($formFactory);

        $this->sessionContainer->currentStep = 'bar';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertTrue($form->has('previous'));
        $this->assertFalse($form->has('next'));
        $this->assertTrue($form->has('valid'));
        $this->assertTrue($form->has('cancel'));
    }

    public function testFormActionAttribute()
    {
        $formFactory = $this->getFormFactory();
        $this->wizard->setFormFactory($formFactory);

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));

        $form = $this->wizard->getForm();
        $action = $form->getAttribute('action');

        $this->assertStringMatchesFormat('?%s=%s', $action);
    }

    public function testSetStepDataDuringProcess()
    {
        $params = new \Zend\Stdlib\Parameters([
            'foo' => 123,
            'bar' => 456,
        ]);
        $this->request
            ->setMethod(Request::METHOD_POST)
            ->setPost($params);

        $this->sessionContainer->currentStep = 'foo';

        $fooStep = $this->getStepMock('foo');
        $fooStep
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(true));

        $steps = $this->wizard->getSteps();
        $steps->add($fooStep);
        $steps->add($this->getStepMock('bar'));

        $this->wizard->process();

        $stepData = $fooStep->getData();
        $this->assertArrayHasKey('foo', $stepData);
        $this->assertArrayHasKey('bar', $stepData);
    }

    public function testCanGoToPreviousStep()
    {
        $params = new \Zend\Stdlib\Parameters(['previous' => true]);
        $this->request
            ->setMethod(Request::METHOD_POST)
            ->setPost($params);

        $this->sessionContainer->currentStep = 'bar';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $this->wizard->process();

        $this->assertEquals('foo', $this->sessionContainer->currentStep);
    }

    public function testCanGoToNextStep()
    {
        $params = new \Zend\Stdlib\Parameters(['step' => []]);
        $this->request
            ->setMethod(Request::METHOD_POST)
            ->setPost($params);

        $this->sessionContainer->currentStep = 'foo';

        $fooStep = $this->getStepMock('foo');
        $fooStep
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(true));

        $steps = $this->wizard->getSteps();
        $steps->add($fooStep);
        $steps->add($this->getStepMock('bar'));

        $this->wizard->process();

        $this->assertEquals('bar', $this->sessionContainer->currentStep);
    }

    public function testCanRedirectAfterLastStep()
    {
        $params = new \Zend\Stdlib\Parameters(['step' => []]);
        $this->request
            ->setMethod(Request::METHOD_POST)
            ->setPost($params);

        $uri = '/foo';
        $this->wizard->getOptions()->setRedirectUrl($uri);

        $fooStep = $this->getStepMock('foo');
        $fooStep
            ->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue(true));

        $steps = $this->wizard->getSteps();
        $steps->add($fooStep);

        $this->wizard->process();

        $this->assertEquals(302, $this->response->getStatusCode());

        $headers = $this->response->getHeaders();
        /* @var $locationHeader \Zend\Http\Header\Location */
        $locationHeader = $headers->get('Location');

        $this->assertEquals($uri, $locationHeader->getUri());
    }

    public function testCanRedirectAfterCancel()
    {
        $params = new \Zend\Stdlib\Parameters(['cancel' => true]);
        $this->request
            ->setMethod(Request::METHOD_POST)
            ->setPost($params);

        $uri = '/cancel';
        $this->wizard->getOptions()->setCancelUrl($uri);

        $this->wizard->process();

        $this->assertEquals(302, $this->response->getStatusCode());

        $headers = $this->response->getHeaders();
        /* @var $locationHeader \Zend\Http\Header\Location */
        $locationHeader = $headers->get('Location');

        $this->assertEquals($uri, $locationHeader->getUri());
    }

    public function testCurrentStepNumber()
    {
        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));
        $this->assertEquals(1, $this->wizard->getCurrentStepNumber());

        $this->sessionContainer->currentStep = 'bar';
        $this->assertEquals(2, $this->wizard->getCurrentStepNumber());
    }

    public function testGetTotalStepCount()
    {
        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $this->assertEquals(2, $this->wizard->getTotalStepCount());
    }

    public function testGetTotalStepCountWithoutSteps()
    {
        $this->assertEquals(0, $this->wizard->getTotalStepCount());
    }

    public function testGetPercentProgress()
    {
        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $this->assertEquals(0, $this->wizard->getPercentProgress());

        $this->wizard->setCurrentStep('bar');
        $this->assertEquals(50, $this->wizard->getPercentProgress());
    }

    public function testGetPercentProgressWithoutSteps()
    {
        $this->assertEquals(0, $this->wizard->getPercentProgress());
    }

    public function testSetAndGetStepCollection()
    {
        $this->assertInstanceOf('Wizard\Step\StepCollection', $this->wizard->getSteps());
    }

    public function testGetCollectionWithRestoredSteps()
    {
        $this->sessionContainer->steps = [
            'foo' => [
                'options' => [
                    'title' => 'Foo',
                ],
                'data'  => [
                    'foo' => 123,
                    'bar' => 456,
                ],
            ],
        ];

        $stepCollection = $this->wizard->getSteps();

        $step = $this->getStepMock('foo');
        $step
            ->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue(new \Zend\Form\Form));
        $stepCollection->add($step);

        $step->getOptions()->setTitle('Foo');

        $this->assertEquals('Foo', $step->getOptions()->getTitle());
        $this->assertInstanceOf('Zend\Form\Form', $step->getForm());
    }

    public function testRender()
    {
        $formFactory = $this->getFormFactory();
        $this->wizard->setFormFactory($formFactory);

        $renderer = new PhpRenderer;
        $resolver = new TemplateMapResolver([
            'wizard/layout'   => __DIR__ . '/_files/layout.phtml',
            'wizard/header'   => __DIR__ . '/_files/header.phtml',
            'wizard/buttons'  => __DIR__ . '/_files/buttons.phtml',
            'wizard/step/foo' => __DIR__ . '/_files/steps/foo.phtml',
        ]);
        $renderer->setResolver($resolver);

        $stepCollection = $this->wizard->getSteps();

        $step = $this->getStepMock('foo');
        $step
            ->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue(new \Zend\Form\Form));

        $step->getOptions()->setViewTemplate('wizard/step/foo');

        $stepCollection->add($step);

        $this->wizard->getOptions()->setLayoutTemplate('wizard/layout');

        $viewModel = $this->wizard->getViewModel();
        $this->assertNotEmpty($viewModel->getTemplate());

        $output = $renderer->render($viewModel);

        $this->assertRegExp('/foo-step/', $output);
    }

    public function testInitViewModelWhenChangeCurrentStep()
    {
        $this->sessionContainer->currentStep = 'foo';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $viewModel = $this->wizard->getViewModel();

        $viewModel->setVariable('foo', 123);
        $this->assertNotNull($viewModel->getVariable('wizard'));
        $this->assertEquals(123, $viewModel->getVariable('foo'));

        $this->wizard->setCurrentStep('bar');
        $this->assertNotNull($viewModel->getVariable('wizard'));
        $this->assertNull($viewModel->getVariable('foo', null));
    }

    /**
     * @param  string $name
     * @return StepInterface
     */
    protected function getStepMock($name)
    {
        $mock = $this->getMockForAbstractClass(
            'Wizard\Step\AbstractStep', [], '', true, true, true, [
                'getName', 'getForm', 'isComplete'
            ]
        );
        $mock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }
}
