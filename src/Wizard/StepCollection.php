<?php
namespace Wizard;

use Zend\EventManager\EventManager;

class StepCollection implements \IteratorAggregate, \Countable
{
    const EVENT_ADD_STEP = 'add-step';

    /**
     * @var array
     */
    protected $steps = array();
    
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
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

        $this->getEventManager()->trigger(self::EVENT_ADD_STEP, $step);

        $this->steps[$step->getName()] = $step;
        return $this;
    }

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
     * @return StepInterface
     */
    public function getFirst()
    {
        if (!$this->steps) {
            return null;
        }

        $values = array_values($this->steps);

        return array_shift($values);
    }

    /**
     * @param  string|StepInterface $identitier
     * @return bool
     */
    public function isFirst($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        $firstStep = $this->getFirst();

        return $identifier === $firstStep->getName();
    }

    /**
     * @return StepInterface
     */
    public function getLast()
    {
        if (!$this->steps) {
            return null;
        }

        $values = array_values($this->steps);

        return array_pop($values);
    }

    /**
     * @param  string|StepInterface $identitier
     * @return bool
     */
    public function isLast($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        $lastStep = $this->getLast();

        return $identifier === $lastStep->getName();
    }

    /**
     * @param  string|StepInterface $identifier
     * @return StepInterface
     */
    public function getPrevious($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        $steps = array_keys($this->steps);
        $position = array_search($identifier, $steps) - 1;

        return isset($steps[$position]) ? $this->get($steps[$position]) : null;
    }

    /**
     * @param  string|StepInterface $identifier
     * @return StepInterface
     */
    public function getNext($identifier)
    {
        if ($identifier instanceof StepInterface) {
            $identifier = $identifier->getName();
        }

        $steps = array_keys($this->steps);
        $position = array_search($identifier, $steps) + 1;

        return isset($steps[$position]) ? $this->get($steps[$position]) : null;
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

    public function __sleep()
    {
        return array('steps');
    }
}