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
                    'WizardTest\TestAsset\Step\Foo' => [
                        'title'         => 'foo',
                        'view_template' => 'wizard/foo',
                        'form'          => 'WizardTest\TestAsset\Step\FooForm',
                    ],
                    'WizardTest\TestAsset\Step\Bar' => [
                        'title'         => 'bar',
                        'view_template' => 'wizard/bar',
                    ],
                    'WizardTest\TestAsset\Step\Baz' => [
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

        $stepFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap([
                [
                    'WizardTest\TestAsset\Step\Foo',
                    $wizardConfig['steps']['WizardTest\TestAsset\Step\Foo'],
                    new \WizardTest\TestAsset\Step\Foo()
                ],
                [
                    'WizardTest\TestAsset\Step\Bar',
                    $wizardConfig['steps']['WizardTest\TestAsset\Step\Bar'],
                    new \WizardTest\TestAsset\Step\Bar()
                ],
                [
                    'WizardTest\TestAsset\Step\Baz',
                    $wizardConfig['steps']['WizardTest\TestAsset\Step\Baz'],
                    new \WizardTest\TestAsset\Step\Baz()
                ],
            ]));

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
}
