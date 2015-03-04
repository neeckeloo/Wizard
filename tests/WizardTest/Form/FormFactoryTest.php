<?php
namespace WizardTest\Form;

use Wizard\Form\FormFactory;
use Wizard\Form\Element\Button as ButtonElement;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateForm()
    {
        $formFactory = new FormFactory();

        $previousButton = new ButtonElement\Previous('previous');
        $nextButton     = new ButtonElement\Next('next');
        $validButton    = new ButtonElement\Valid('valid');
        $cancelButton   = new ButtonElement\Cancel('cancel');

        $returnValueMap = [
            ['Wizard\Form\Element\Button\Previous', $previousButton],
            ['Wizard\Form\Element\Button\Next',     $nextButton],
            ['Wizard\Form\Element\Button\Valid',    $validButton],
            ['Wizard\Form\Element\Button\Cancel',   $cancelButton],
        ];

        $formElementManagerStub = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $formElementManagerStub
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValue($formElementManagerStub));

        $formFactory->setServiceManager($serviceManagerStub);

        /* @var $form \Zend\Form\Form */
        $form = $formFactory->create();

        $this->assertInstanceOf('Zend\Form\Form', $form);
        $this->assertCount(4, $form->getElements());
    }
}
