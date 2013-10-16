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
     * @param  string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param  Wizard $wizard
     * @return self
     */
    public function setWizard(Wizard $wizard);

    /**
     * @return Wizard
     */
    public function getWizard();

    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form);

    /**
     * @return Form
     */
    public function getForm();

    /**
     * @param  string $template
     * @return self
     */
    public function setViewTemplate($template);

    /**
     * @return string
     */
    public function getViewTemplate();

    /**
     * @param  array $data
     * @return self
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
     * @return self
     */
    public function setComplete($complete = true);

    /**
     * @return bool
     */
    public function isComplete();

    /**
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function setFromArray($options);

    /**
     * @return array
     */
    public function toArray();
}