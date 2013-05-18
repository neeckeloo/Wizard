<?php
namespace Wizard;

use Wizard\Wizard;
use Zend\Form\Form;
use Traversable;

interface StepInterface
{
    /**
     * @void
     */
    public function init();

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
     * @param  Wizard $wizard
     * @return StepInterface
     */
    public function setWizard(Wizard $wizard);

    /**
     * @return Wizard
     */
    public function getWizard();

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
     * @param  string $template
     * @return StepInterface
     */
    public function setViewTemplate($template);

    /**
     * @return string
     */
    public function getViewTemplate();

    /**
     * @param  array $data
     * @return StepInterface
     */
    public function setData(array $data);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param  array $data
     * @return void|bool
     */
    public function process(array $data);

    /**
     * @param  bool $complete
     * @return StepInterface
     */
    public function setComplete($complete = true);

    /**
     * @return bool
     */
    public function isComplete();

    /**
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return StepInterface
     */
    public function setFromArray($options);

    /**
     * @return array
     */
    public function toArray();
}