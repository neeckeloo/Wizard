<?php
namespace WizardTest\Form;

use Wizard\Form\FormFactory;
use Wizard\Form\Element\Button as ButtonElement;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    public function setUp()
    {
        $this->serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $this->formFactory = new FormFactory();
    }

    public function testCreateForm()
    {
        $previousButton = new ButtonElement\Previous('previous');
        $this->serviceManager
            ->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Previous'))
            ->will($this->returnValue($previousButton));

        $nextButton = new ButtonElement\Next('next');
        $this->serviceManager
            ->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Next'))
            ->will($this->returnValue($nextButton));

        $validButton = new ButtonElement\Valid('valid');
        $this->serviceManager
            ->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Valid'))
            ->will($this->returnValue($validButton));

        $cancelButton = new ButtonElement\Cancel('cancel');
        $this->serviceManager
            ->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Cancel'))
            ->will($this->returnValue($cancelButton));

        $this->formFactory->setServiceManager($this->serviceManager);

        /* @var $form \Zend\Form\Form */
        $form = $this->formFactory->create();
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertCount(4, $form->getElements());
    }
}