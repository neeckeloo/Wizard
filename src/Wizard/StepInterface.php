<?php
namespace Wizard;

use Zend\Form\Form;

interface StepInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param  string $title
     * @return StepInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param  Form $form
     * @return StepInterface
     */
    public function setForm(Form $form);

    /**
     * @return Form
     */
    public function getForm();

    /**
     * @param  array $data
     * @return void
     */
    public function process(array $data);
}