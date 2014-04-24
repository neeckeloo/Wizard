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
        $form = new Form();
        $form
            ->add($this->serviceManager->get('Wizard\Form\Element\Button\Previous'))
            ->add($this->serviceManager->get('Wizard\Form\Element\Button\Next'))
            ->add($this->serviceManager->get('Wizard\Form\Element\Button\Valid'))
            ->add($this->serviceManager->get('Wizard\Form\Element\Button\Cancel'));

        return $form;
    }
}