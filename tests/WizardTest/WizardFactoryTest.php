<?php
namespace WizardTest;

use Wizard\WizardFactory;
use Zend\ServiceManager\ServiceManager;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        'default_layout_template' => 'wizard/layout',
        'wizards' => [
            'Wizard\Foo' => [
                'layout_template' => 'wizard/custom-layout',
                'redirect_url'    => '/foo',
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

    public function testCreateWizard()
    {
        $wizardFactory = new WizardFactory($this->config);

        $stepFactoryMock = $this->getStepFactory();

        $wizardConfig = $this->config['wizards']['Wizard\Foo'];

        $returnValueMap = [];
        foreach ($wizardConfig['steps'] as $name => $config) {
            $returnValueMap[] = [$name, $config, $this->getStep()];
        }

        $stepFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap($returnValueMap));

        $wizardFactory->setStepFactory($stepFactoryMock);

        $stepCollectionMock = $this->getMock('Wizard\Step\StepCollection');

        $stepCollectionMock
            ->expects($this->exactly(count($wizardConfig['steps'])))
            ->method('add');

        $wizardOptionsMock = $this->getMock('Wizard\WizardOptions');

        $wizardOptionsMock
            ->expects($this->once())
            ->method('setFromArray')
            ->with($wizardConfig);

        $wizardMock = $this->getMock('Wizard\Wizard');

        $wizardMock
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($wizardOptionsMock));

        $wizardMock
            ->expects($this->any())
            ->method('getSteps')
            ->will($this->returnValue($stepCollectionMock));

        $serviceManager = new ServiceManager();
        $serviceManager->setService('Wizard\Wizard', $wizardMock);
        $wizardFactory->setServiceManager($serviceManager);

        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
    }

    /**
     * @expectedException \Wizard\Exception\RuntimeException
     */
    public function testCreateInvalidWizard()
    {
        $wizardFactory = new WizardFactory([]);
        $wizardFactory->create('invalid');
    }

    private function getStepFactory()
    {
        return $this->getMockBuilder('Wizard\Step\StepFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getStep()
    {
        $stepMock = $this->getMock('Wizard\Step\StepInterface');

        $stepMock
            ->expects($this->once())
            ->method('setWizard')
            ->will($this->returnSelf());

        $stepMock
            ->expects($this->once())
            ->method('init');

        return $stepMock;
    }
}
