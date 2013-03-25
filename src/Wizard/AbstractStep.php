<?php
namespace Wizard;

use Zend\Form\Form;
use Traversable;

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
     * @var string
     */
    protected $viewTemplate;

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
        return $this;
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
    public function setViewTemplate($template)
    {
        $this->viewTemplate = (string) $template;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getViewTemplate()
    {
        return $this->viewTemplate;
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

    /**
     * {@inheritDoc}
     */
    public function setFromArray($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter provided to %s must be an array or Traversable',
                __METHOD__
            ));
        }

        foreach ($options as $key => $value) {
            $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
            $method = 'set' . ucfirst($filter->filter($key));
            if (!method_exists($this, $method)) {
                continue;
            }

            $this->$method($value);
        }
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $vars = get_object_vars($this);

        $options = array();
        foreach ($vars as $key => $value) {
            if ($key == 'form') {
                continue;
            }

            $options[$key] = $value;
        }

        return $options;
    }
}