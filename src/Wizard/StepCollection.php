<?php
namespace Wizard;

class StepCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $steps = array();

    /**
     * @param  string|StepInterface $identifier
     * @return StepCollection
     */
    public function remove($identifier)
    {
        if (!$this->has($identifier)) {
            return $this;
        }

        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        unset($this->steps[$identifier]);
        return $this;
    }

    /**
     * @param  StepInterface $step
     * @return StepCollection
     */
    public function add(StepInterface $step)
    {
        if ($this->has($step)) {
            return $this;
        }

        $this->steps[$step->getName()] = $step;
        return $this;
    }

    /**
     * @param  string|StepInterface $identifier
     * @return StepInterface
     */
    public function get($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        return $this->has($identifier) ? $this->steps[$identifier] : null;
    }

    /**
     * @param  string|StepInterface $identifier
     * @return bool
     */
    public function has($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        return isset($this->steps[$identifier]);
    }

    /**
     * @see IteratorAggregate
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->steps);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->steps);
    }
}