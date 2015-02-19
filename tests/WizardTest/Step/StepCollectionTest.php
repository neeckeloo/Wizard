<?php
namespace WizardTest;

use Wizard\Step\StepCollection;

class StepCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StepCollection
     */
    protected $stepCollection;

    public function setUp()
    {
        $this->stepCollection = new StepCollection();
    }

    public function testCountable()
    {
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $this->stepCollection->add($step);
        }

        $this->assertCount(3, $this->stepCollection);
    }

    public function testIteratorAggregate()
    {
        $this->assertInstanceOf('ArrayIterator', $this->stepCollection->getIterator());

        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $this->stepCollection->add($step);
        }

        $i = 1;
        foreach ($this->stepCollection as $step) {
            $this->assertEquals('step' . $i, $step->getName());
            $i++;
        }
    }

    public function testGetStep()
    {
        $step = $this->getStepMock('foo');
        $this->stepCollection->add($step);

        $this->assertInstanceOf('Wizard\Step\StepInterface', $this->stepCollection->get('foo'));
        $this->assertNull($this->stepCollection->get('bar'));
    }

    public function testHasStep()
    {
        $step = $this->getStepMock('foo');
        $this->stepCollection->add($step);

        $this->assertTrue($this->stepCollection->has('foo'));
        $this->assertFalse($this->stepCollection->has('bar'));
    }

    public function testRemoveStep()
    {
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $this->stepCollection->add($step);
        }

        $this->stepCollection->remove('step2');

        $this->assertCount(2, $this->stepCollection);
    }

    public function testGetFirst()
    {
        $this->stepCollection->add($this->getStepMock('foo'));
        $this->stepCollection->add($this->getStepMock('bar'));

        $firstStep = $this->stepCollection->getFirst();
        $this->assertEquals('foo', $firstStep->getName());
    }

    public function testIsFirst()
    {
        $this->stepCollection->add($this->getStepMock('foo'));
        $this->stepCollection->add($this->getStepMock('bar'));

        $this->assertTrue($this->stepCollection->isFirst('foo'));
        $this->assertFalse($this->stepCollection->isFirst('bar'));
    }

    public function testGetLast()
    {
        $this->stepCollection->add($this->getStepMock('foo'));
        $this->stepCollection->add($this->getStepMock('bar'));

        $firstStep = $this->stepCollection->getLast();
        $this->assertEquals('bar', $firstStep->getName());
    }

    public function testIsLast()
    {
        $this->stepCollection->add($this->getStepMock('foo'));
        $this->stepCollection->add($this->getStepMock('bar'));

        $this->assertFalse($this->stepCollection->isLast('foo'));
        $this->assertTrue($this->stepCollection->isLast('bar'));
    }

    public function testGetPreviousStep()
    {
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $this->stepCollection->add($step);
        }

        $this->assertNull($this->stepCollection->getPrevious('step1'));
        $this->assertNotNull($this->stepCollection->getPrevious('step2'));
        $this->assertNotNull($this->stepCollection->getPrevious('step3'));
    }

    public function testGetNextStep()
    {
        for ($i = 1; $i <= 3; $i++) {
            $step = $this->getStepMock('step' . $i);
            $this->stepCollection->add($step);
        }

        $this->assertNotNull($this->stepCollection->getNext('step1'));
        $this->assertNotNull($this->stepCollection->getNext('step2'));
        $this->assertNull($this->stepCollection->getNext('step3'));
    }

    /**
     * @param  string $name
     * @return StepInterface
     */
    protected function getStepMock($name)
    {
        $mock = $this->getMock('Wizard\Step\StepInterface');
        $mock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }
}
