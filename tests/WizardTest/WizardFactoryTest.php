<?php
namespace WizardTest;

use Wizard\WizardFactory;

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

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManagerStub
            ->method('get')
            ->with('Wizard\Wizard')
            ->will($this->returnValue($this->getWizard()));
        $wizardFactory->setServiceManager($serviceManagerStub);

        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
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

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManagerStub
            ->method('get')
            ->with('Wizard\Wizard')
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

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManagerStub
            ->method('get')
            ->with('Wizard\Wizard')
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

        $serviceManagerStub = $this->getMock('Zend\ServiceManager\ServiceManager');
        $serviceManagerStub
            ->method('get')
            ->with('Wizard\Wizard')
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
        $wizard = $this->getMock('Wizard\Wizard');

        $wizard
            ->method('getOptions')
            ->will($this->returnValue($this->getWizardOptions()));

        $wizard
            ->method('getViewModel')
            ->will($this->returnValue($this->getViewModel()));

        $stepCollectionMock = $this->getMock('Wizard\Step\StepCollection');

        $wizard
            ->method('getSteps')
            ->will($this->returnValue($stepCollectionMock));

        return $wizard;
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder('Wizard\Step\StepFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getStep()
    {
        $step = $this->getMock('Wizard\Step\StepInterface');
        $step->method('setWizard')->will($this->returnSelf());

        return $step;
    }

    private function getViewModel()
    {
        return $this->getMock('Zend\View\Model\ViewModel');
    }

    private function getWizardOptions()
    {
        return $this->getMock('Wizard\WizardOptions');
    }
}
