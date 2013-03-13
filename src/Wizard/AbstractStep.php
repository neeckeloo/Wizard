<?php
namespace Wizard;

use Zend\Form\Form;

abstract class AbstractStep implements StepInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var Form
     */
    protected $form;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        $filter = new \Zend\Filter\Word\CamelCaseToUnderscore();

        $parts = explode('\\', get_called_class());
        $name = array_pop($parts);

        return strtolower($filter->filter($name));
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $data)
    {

    }
}