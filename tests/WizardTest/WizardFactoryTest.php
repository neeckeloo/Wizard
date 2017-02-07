<?php
namespace WizardTest;

use Wizard\WizardFactory;
use Wizard\WizardOptions;
use Zend\View\Model\ViewModel;
use Wizard\Step\StepInterface;
use Wizard\Step\StepFactory;
use Wizard\Step\StepCollection;
use Wizard\Wizard;
use Zend\ServiceManager\ServiceManager;
use Wizard\WizardInterface;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        'default_layout_template' => 'wizard/layout',
        'wizards' => [
            'Wizard\Foo' => [
                'layout_template' => 'wizard/custom-layout',
                'redirect_url'    => '/foo',
                'cancel_url'      => '/bar',
                'steps' => [
                    'App\Step\Foo' => [
                        'title'         => 'foo',
                        'view_template' => 'wizard/foo',
                        'form'          => 'App\Step\FooForm',
                    ],
                    'App\Step\Bar' => [
                        'title'         => 'bar',
                        'view_template' => 'wizard/bar',
                    ],
                    'App\Step\Baz' => [
                        'title'         => 'baz',
                        'view_template' => 'wizard/baz',
                    ],
                ],
            ],
        ],
    ];

    public function testCreateWizardShouldReturnInstance()
    {
        $wizardFactory = new WizardFactory($this->config);

        $stepFactoryStub = $this->getStepFactory();
        $wizardFactory->setStepFactory($stepFactoryStub);

        $serviceManagerStub = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with(Wizard::class)
            ->will($this->returnValue($this->getWizard()));
        $wizardFactory->setServiceManager($serviceManagerStub);

        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf(WizardInterface::class, $wizard);
    }

    public function testCreateWizardShouldAddSteps()
    {
        $wizardFactory = new WizardFactory($this->config);

        $returnValueMap = [];
        foreach ($this->config['wizards']['Wizard\Foo']['steps'] as $name => $config) {
            $returnValueMap[] = [$name, $config, $this->getStep()];
        }

        $stepFactoryStub = $this->getStepFactory();
        $stepFactoryStub
            ->method('create')
            ->will($this->returnValueMap($returnValueMap));
        $wizardFactory->setStepFactory($stepFactoryStub);

        $wizardStub = $this->getWizard();

        $stepCollectionMock = $wizardStub->getSteps();
        $stepCollectionMock
            ->expects($this->exactly(count($returnValueMap)))
            ->method('add');

        $serviceManagerStub = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with(Wizard::class)
            ->will($this->returnValue($wizardStub));
        $wizardFactory->setServiceManager($serviceManagerStub);

        $wizardFactory->create('Wizard\Foo');
    }

    public function testCreateWizardShouldConfigureWizardOptions()
    {
        $wizardFactory = new WizardFactory($this->config);

        $stepFactoryStub = $this->getStepFactory();
        $wizardFactory->setStepFactory($stepFactoryStub);

        $wizardStub = $this->getWizard();

        $wizardOptionsMock = $wizardStub->getOptions();
        $wizardOptionsMock
            ->expects($this->once())
            ->method('setFromArray')
            ->with($this->isType('array'));

        $serviceManagerStub = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with(Wizard::class)
            ->will($this->returnValue($wizardStub));
        $wizardFactory->setServiceManager($serviceManagerStub);

        $wizardFactory->create('Wizard\Foo');
    }

    public function testCreateWizardShouldConfigureLayoutTemplate()
    {
        $wizardFactory = new WizardFactory($this->config);

        $stepFactoryStub = $this->getStepFactory();
        $wizardFactory->setStepFactory($stepFactoryStub);

        $wizardStub = $this->getWizard();

        $viewModelMock = $wizardStub->getViewModel();
        $viewModelMock
            ->expects($this->once())
            ->method('setTemplate')
            ->with($this->anything());

        $serviceManagerStub = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceManagerStub
            ->method('get')
            ->with(Wizard::class)
            ->will($this->returnValue($wizardStub));
        $wizardFactory->setServiceManager($serviceManagerStub);

        $wizardFactory->create('Wizard\Foo');
    }

    /**
     * @expectedException \Wizard\Exception\RuntimeException
     */
    public function testCreateInvalidWizard()
    {
        $wizardFactory = new WizardFactory([]);
        $wizardFactory->create('invalid');
    }

    private function getWizard()
    {
        $wizard = $this->getMockBuilder(Wizard::class)
            ->getMock();

        $wizard
            ->method('getOptions')
            ->will($this->returnValue($this->getWizardOptions()));

        $wizard
            ->method('getViewModel')
            ->will($this->returnValue($this->getViewModel()));

        $stepCollectionMock = $this->getMockBuilder(StepCollection::class)
            ->getMock();

        $wizard
            ->method('getSteps')
            ->will($this->returnValue($stepCollectionMock));

        return $wizard;
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder(StepFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getStep()
    {
        $step = $this->getMockBuilder(StepInterface::class)
            ->getMock();
        $step->method('setWizard')->will($this->returnSelf());

        return $step;
    }

    private function getViewModel()
    {
        return $this->getMockBuilder(ViewModel::class)
            ->getMock();
    }

    private function getWizardOptions()
    {
        return $this->getMockBuilder(WizardOptions::class)
            ->getMock();
    }
}
