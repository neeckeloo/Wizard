<?php
namespace WizardTest\Step;

use Wizard\Step\StepCollection;
use Wizard\Step\StepInterface;

class StepCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCountable()
    {
        $stepCollection = new StepCollection();

        for ($i = 1; $i <= 3; $i++) {
            $stepStub = $this->getStepMock('step' . $i);
            $stepCollection->add($stepStub);
        }

        $this->assertCount(3, $stepCollection);
    }

    public function testIteratorAggregate()
    {
        $stepCollection = new StepCollection();
        $this->assertInstanceOf(\ArrayIterator::class, $stepCollection->getIterator());

        for ($i = 1; $i <= 3; $i++) {
            $stepStub = $this->getStepMock('step' . $i);
            $stepCollection->add($stepStub);
        }

        $i = 1;
        foreach ($stepCollection as $step) {
            $this->assertEquals('step' . $i, $step->getName());
            $i++;
        }
    }

    public function testGetStep()
    {
        $stepCollection = new StepCollection();

        $stepStub = $this->getStepMock('foo');
        $stepCollection->add($stepStub);

        $this->assertInstanceOf(StepInterface::class, $stepCollection->get('foo'));
        $this->assertNull($stepCollection->get('bar'));
    }

    public function testHasStep()
    {
        $stepCollection = new StepCollection();

        $stepStub = $this->getStepMock('foo');
        $stepCollection->add($stepStub);

        $this->assertTrue($stepCollection->has('foo'));
        $this->assertFalse($stepCollection->has('bar'));
    }

    public function testRemoveStep()
    {
        $stepCollection = new StepCollection();

        for ($i = 1; $i <= 3; $i++) {
            $stepStub = $this->getStepMock('step' . $i);
            $stepCollection->add($stepStub);
        }

        $stepCollection->remove('step2');

        $this->assertCount(2, $stepCollection);
    }

    public function testGetFirst()
    {
        $stepCollection = new StepCollection();

        $stepCollection->add($this->getStepMock('foo'));
        $stepCollection->add($this->getStepMock('bar'));

        $firstStep = $stepCollection->getFirst();
        $this->assertEquals('foo', $firstStep->getName());
    }

    public function testIsFirst()
    {
        $stepCollection = new StepCollection();

        $stepCollection->add($this->getStepMock('foo'));
        $stepCollection->add($this->getStepMock('bar'));

        $this->assertTrue($stepCollection->isFirst('foo'));
        $this->assertFalse($stepCollection->isFirst('bar'));
    }

    public function testGetLast()
    {
        $stepCollection = new StepCollection();

        $stepCollection->add($this->getStepMock('foo'));
        $stepCollection->add($this->getStepMock('bar'));

        $firstStep = $stepCollection->getLast();
        $this->assertEquals('bar', $firstStep->getName());
    }

    public function testIsLast()
    {
        $stepCollection = new StepCollection();

        $stepCollection->add($this->getStepMock('foo'));
        $stepCollection->add($this->getStepMock('bar'));

        $this->assertFalse($stepCollection->isLast('foo'));
        $this->assertTrue($stepCollection->isLast('bar'));
    }

    public function testGetPreviousStep()
    {
        $stepCollection = new StepCollection();

        for ($i = 1; $i <= 3; $i++) {
            $stepStub = $this->getStepMock('step' . $i);
            $stepCollection->add($stepStub);
        }

        $this->assertNull($stepCollection->getPrevious('step1'));
        $this->assertNotNull($stepCollection->getPrevious('step2'));
        $this->assertNotNull($stepCollection->getPrevious('step3'));
    }

    public function testGetNextStep()
    {
        $stepCollection = new StepCollection();

        for ($i = 1; $i <= 3; $i++) {
            $stepStub = $this->getStepMock('step' . $i);
            $stepCollection->add($stepStub);
        }

        $this->assertNotNull($stepCollection->getNext('step1'));
        $this->assertNotNull($stepCollection->getNext('step2'));
        $this->assertNull($stepCollection->getNext('step3'));
    }

    /**
     * @param  string $name
     * @return StepInterface
     */
    protected function getStepMock($name)
    {
        $mock = $this->getMockBuilder(StepInterface::class)
            ->getMock();
        $mock
            ->method('getName')
            ->will($this->returnValue($name));

        return $mock;
    }
}
