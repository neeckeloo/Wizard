<?php
namespace Wizard;

use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage as SessionStorage;

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

    public function setUp()
    {
        $request = $this->getMock('Zend\Http\Request');
        $response = $this->getMock('Zend\Http\Response');

        $routeMatch = $this->getMock('Zend\Mvc\Router\RouteMatch', array(), array(), '', false);
        $routeMatch
            ->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue('foo'));

        $sessionStorage = new SessionStorage;
        $sessionManager = new SessionManager(null, $sessionStorage);

        $this->wizard = $this->getMock(
            'Wizard\Wizard',
            array('getSessionContainer'),
            array($request, $response, $routeMatch, $sessionManager)
        );

        $this->sessionContainer = new SessionContainer('foo', $sessionManager);

        $this->wizard
            ->expects($this->any())
            ->method('getSessionContainer')
            ->will($this->returnValue($this->sessionContainer));
    }

    public function testGetCurrentStep()
    {
        $this->assertNull($this->wizard->getCurrentStep());

        $steps = $this->wizard->getSteps();
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $steps->add($step);
        }

        $this->assertInstanceOf('Wizard\StepInterface', $this->wizard->getCurrentStep());
    }

    public function testGetSteps()
    {
        $this->assertInstanceOf('Wizard\StepCollection', $this->wizard->getSteps());
    }

    public function testGetFormWithoutSteps()
    {
        $this->assertNull($this->wizard->getForm());
    }

    public function testGetFormOfFirstStep()
    {
        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertFalse($form->has('previous'));
        $this->assertTrue($form->has('submit'));

        $submitButton = $form->get('submit');
        $this->assertEquals('Suivant', $submitButton->getLabel());
    }

    public function testGetFormOfMiddleStep()
    {
        $this->sessionContainer->currentStep = 'bar';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));
        $steps->add($this->getStepMock('baz'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertTrue($form->has('previous'));
        $this->assertTrue($form->has('submit'));

        $submitButton = $form->get('submit');
        $this->assertEquals('Suivant', $submitButton->getLabel());
    }

    public function testGetFormOfLastStep()
    {
        $this->sessionContainer->currentStep = 'bar';

        $steps = $this->wizard->getSteps();
        $steps->add($this->getStepMock('foo'));
        $steps->add($this->getStepMock('bar'));

        $form = $this->wizard->getForm();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertTrue($form->has('previous'));
        $this->assertTrue($form->has('submit'));

        $submitButton = $form->get('submit');
        $this->assertEquals('Valider', $submitButton->getLabel());
    }

    public function testSetAndGetStepCollection()
    {
        $this->assertInstanceOf('Wizard\StepCollection', $this->wizard->getSteps());
    }

    /**
     * @param  string $name
     * @return StepInterface
     */
    protected function getStepMock($name)
    {
        $mock = $this->getMock('Wizard\StepInterface');
        $mock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }
}