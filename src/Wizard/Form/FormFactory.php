<?php
namespace Wizard\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use Zend\Form\FormInterface;

class FormFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * FormFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return FormInterface
     */
    public function create()
    {
        $formElementManager = $this->container->get('FormElementManager');

        $form = new Form();
        $form
            ->add($formElementManager->get(Element\Button\Previous::class))
            ->add($formElementManager->get(Element\Button\Next::class))
            ->add($formElementManager->get(Element\Button\Valid::class))
            ->add($formElementManager->get(Element\Button\Cancel::class));

        return $form;
    }
}