<?php
namespace WizardTest;

use Wizard\Wizard;
use Wizard\WizardFactory;
use Zend\ServiceManager\ServiceManager;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizard()
    {
        $config = [
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

        $wizardFactory = new WizardFactory($config);

        $stepPluginManager = new ServiceManager();
        $stepPluginManager
            ->setService('WizardTest\TestAsset\Step\Foo', new \WizardTest\TestAsset\Step\Foo())
            ->setService('WizardTest\TestAsset\Step\Bar', new \WizardTest\TestAsset\Step\Bar())
            ->setService('WizardTest\TestAsset\Step\Baz', new \WizardTest\TestAsset\Step\Baz());

        $formElementManager = new ServiceManager();
        $formElementManager->setService(
            'WizardTest\TestAsset\Step\FooForm',
            $this->getMock('Zend\Form\Form')
        );

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Wizard\Wizard', new Wizard())
            ->setService('Wizard\Step\StepPluginManager', $stepPluginManager)
            ->setService('FormElementManager', $formElementManager)
            ->setService('WizardTest\TestAsset\Step\Foo', $this->getMockForAbstractClass('Wizard\Step\AbstractStep'))
            ->setService('WizardTest\TestAsset\Step\Bar', $this->getMockForAbstractClass('Wizard\Step\AbstractStep'))
            ->setService('WizardTest\TestAsset\Step\Baz', $this->getMockForAbstractClass('Wizard\Step\AbstractStep'));
        $wizardFactory->setServiceManager($serviceManager);

        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);

        $this->assertEquals('wizard/custom-layout', $wizard->getOptions()->getLayoutTemplate());
        $this->assertEquals('/foo', $wizard->getOptions()->getRedirectUrl());

        $steps = $wizard->getSteps();
        $this->assertCount(3, $steps);

        $fooStep = $steps->get('WizardTest\TestAsset\Step\Foo');
        $this->assertEquals('foo', $fooStep->getOptions()->getTitle());
        $this->assertEquals('wizard/foo', $fooStep->getOptions()->getViewTemplate());
        $this->assertInstanceOf('Zend\Form\Form', $fooStep->getForm());

        $barStep = $steps->get('WizardTest\TestAsset\Step\Bar');
        $this->assertEquals('bar', $barStep->getOptions()->getTitle());
        $this->assertEquals('wizard/bar', $barStep->getOptions()->getViewTemplate());
        $this->assertNull($barStep->getForm());

        $bazStep = $steps->get('WizardTest\TestAsset\Step\Baz');
        $this->assertEquals('baz', $bazStep->getOptions()->getTitle());
        $this->assertEquals('wizard/baz', $bazStep->getOptions()->getViewTemplate());
        $this->assertNull($bazStep->getForm());
    }

    public function testCreateWizardWithDefaultOptions()
    {
        $config = [
            'default_layout_template' => 'wizard/layout',
            'wizards' => [
                'Wizard\Foo' => [],
            ],
        ];

        $wizardFactory = new WizardFactory($config);

        $serviceManager = new ServiceManager();
        $serviceManager->setService('Wizard\Wizard', new Wizard());
        $wizardFactory->setServiceManager($serviceManager);

        $wizard = $wizardFactory->create('Wizard\Foo');

        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
        $this->assertEquals('wizard/layout', $wizard->getOptions()->getLayoutTemplate());
    }

    /**
     * @expectedException \Wizard\Exception\RuntimeException
     */
    public function testCreateInvalidWizard()
    {
        $wizardFactory = new WizardFactory([]);
        $wizardFactory->create('invalid');
    }
}
