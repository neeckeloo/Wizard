<?php
namespace Wizard\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class FormFactory implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return FormInterface
     */
    public function create()
    {
        $formElementManager = $this->serviceManager->get('FormElementManager');

        $form = new Form();
        $form
            ->add($formElementManager->get('Wizard\Form\Element\Button\Previous'))
            ->add($formElementManager->get('Wizard\Form\Element\Button\Next'))
            ->add($formElementManager->get('Wizard\Form\Element\Button\Valid'))
            ->add($formElementManager->get('Wizard\Form\Element\Button\Cancel'));

        return $form;
    }
}