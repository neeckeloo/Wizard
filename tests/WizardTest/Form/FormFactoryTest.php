<?php
namespace WizardTest\Form;

use Wizard\Form\FormFactory;
use Wizard\Form\Element\Button as ButtonElement;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateForm()
    {
        $formElementManager = $this->getMock('Zend\ServiceManager\ServiceManager');

        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManager
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($formElementManager));

        $formFactory = new FormFactory();

        $previousButton = new ButtonElement\Previous('previous');
        $formElementManager
            ->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Previous'))
            ->will($this->returnValue($previousButton));

        $nextButton = new ButtonElement\Next('next');
        $formElementManager
            ->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Next'))
            ->will($this->returnValue($nextButton));

        $validButton = new ButtonElement\Valid('valid');
        $formElementManager
            ->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Valid'))
            ->will($this->returnValue($validButton));

        $cancelButton = new ButtonElement\Cancel('cancel');
        $formElementManager
            ->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('Wizard\Form\Element\Button\Cancel'))
            ->will($this->returnValue($cancelButton));

        $formFactory->setServiceManager($serviceManager);

        /* @var $form \Zend\Form\Form */
        $form = $formFactory->create();

        $this->assertInstanceOf('Zend\Form\Form', $form);
        $this->assertCount(4, $form->getElements());
    }
}
