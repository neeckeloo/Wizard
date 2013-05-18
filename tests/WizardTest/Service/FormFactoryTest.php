<?php
namespace WizardTest\Service;

use Wizard\Form\Element\Button as ButtonElement;
use Wizard\Service\FormFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->formFactory = new FormFactory();
    }

    public function testCreateForm()
    {
        $previousButton = new ButtonElement\Previous('previous');
        $this->serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Previous'))
            ->will($this->returnValue($previousButton));

        $nextButton = new ButtonElement\Next('next');
        $this->serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Next'))
            ->will($this->returnValue($nextButton));

        $validButton = new ButtonElement\Valid('valid');
        $this->serviceLocator
            ->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Valid'))
            ->will($this->returnValue($validButton));

        $cancelButton = new ButtonElement\Cancel('cancel');
        $this->serviceLocator
            ->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Cancel'))
            ->will($this->returnValue($cancelButton));

        /* @var $form \Zend\Form\Form */
        $form = $this->formFactory->createService($this->serviceLocator);
        $this->assertInstanceOf('Zend\Form\Form', $form);

        $this->assertCount(4, $form->getElements());
    }
}