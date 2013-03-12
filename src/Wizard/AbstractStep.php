<?php
namespace Wizard;

abstract class AbstractStep implements StepInterface
{
    /**
     * @var string
     */
    protected $title;

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
    public function process()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function isValid(array $data)
    {
        return false;
    }
}