<?php
namespace WizardTest;

use Wizard\Wizard;
use Wizard\WizardFactory;
use Zend\ServiceManager\ServiceManager;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizard()
    {
        $config = array(
            'default_layout_template' => 'wizard/layout',
            'wizards' => array(
                'Wizard\Foo' => array(
                    'layout_template' => 'wizard/custom-layout',
                    'redirect_url'    => '/foo',
                    'steps' => array(
                        'WizardTest\TestAsset\Step\Foo' => array(
                            'title'         => 'foo',
                            'view_template' => 'wizard/foo',
                            'form'          => 'WizardTest\TestAsset\Step\FooForm',
                        ),
                        'WizardTest\TestAsset\Step\Bar' => array(
                            'title'         => 'bar',
                            'view_template' => 'wizard/bar',
                        ),
                        'WizardTest\TestAsset\Step\Baz' => array(
                            'title'         => 'baz',
                            'view_template' => 'wizard/baz',
                        ),
                    ),
                ),
            ),
        );

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
        $config = array(
            'default_layout_template' => 'wizard/layout',
            'wizards' => array(
                'Wizard\Foo' => array(),
            ),
        );

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
        $wizardFactory = new WizardFactory(array());
        $wizardFactory->create('invalid');
    }
}