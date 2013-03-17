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
     * @var array
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $complete = false;

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
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $data)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function setComplete($complete = true)
    {
        $this->complete = (bool) $complete;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplete()
    {
        return $this->complete;
    }

    public function __sleep()
    {
        return array('title', 'data', 'complete');
    }
}