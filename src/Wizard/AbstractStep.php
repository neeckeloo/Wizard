<?php
namespace Wizard;

use Wizard\Wizard;
use Zend\Form\FormInterface;
use Traversable;

abstract class AbstractStep implements StepInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var StepOptionsInterface
     */
    protected $options;

    /**
     * @var Wizard
     */
    protected $wizard;

    /**
     * @var FormInterface
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

    public function init()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        if (!$options instanceof StepOptionsInterface) {
            $options = new StepOptions($options);
        }
        
        $this->options = $options;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        if (!isset($this->options)) {
            $this->setOptions(new StepOptions());
        }

        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function setWizard(Wizard $wizard)
    {
        $this->wizard = $wizard;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWizard()
    {
        return $this->wizard;
    }

    /**
     * {@inheritDoc}
     */
    public function setForm(FormInterface $form)
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
        $form = $this->getForm();
        if (!($form instanceof FormInterface)) {
            return null;
        }

        $form->setData($data);

        return $form->isValid();
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

        $excluded = array('form', 'wizard');

        $options = array();
        foreach ($vars as $key => $value) {
            if (in_array($key, $excluded)) {
                continue;
            }

            if ($value instanceof StepOptionsInterface) {
                $value = $value->toArray();
            }

            $options[$key] = $value;
        }

        return $options;
    }
}