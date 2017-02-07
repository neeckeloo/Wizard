<?php
namespace WizardTest\Form;

use Interop\Container\ContainerInterface;
use Wizard\Form\FormFactory;
use Wizard\Form\Element\Button as ButtonElement;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Wizard\Form\Element\Button\Cancel;
use Wizard\Form\Element\Button\Valid;
use Wizard\Form\Element\Button\Next;
use Wizard\Form\Element\Button\Previous;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateForm()
    {
        $previousButton = new ButtonElement\Previous('previous');
        $nextButton     = new ButtonElement\Next('next');
        $validButton    = new ButtonElement\Valid('valid');
        $cancelButton   = new ButtonElement\Cancel('cancel');

        $returnValueMap = [
            [Previous::class, $previousButton],
            [Next::class,     $nextButton],
            [Valid::class,    $validButton],
            [Cancel::class,   $cancelButton],
        ];

        $formElementManagerStub = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $formElementManagerStub
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($returnValueMap));

        $serviceManagerStub = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->will($this->returnValue($formElementManagerStub));

        $formFactory = new FormFactory($serviceManagerStub);

        /* @var $form \Zend\Form\Form */
        $form = $formFactory->create();

        $this->assertInstanceOf(Form::class, $form);
        $this->assertCount(4, $form->getElements());
    }
}
