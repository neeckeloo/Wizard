<?php
namespace WizardTest;

use Wizard\WizardFactory;
use Wizard\Service\WizardInitializer;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var WizardInitializer
     */
    protected $initializer;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->initializer = $this->getMock('Wizard\Service\WizardInitializer');
    }

    public function testCreateWizard()
    {
        $config = array(
            'wizard' => array(
                'default_layout_template' => 'wizard/layout',
                'default_class'           => 'Wizard\Wizard',
                'wizards' => array(
                    'foo' => array(
                        'class'           => 'WizardTest\TestAsset\Foo',
                        'layout_template' => 'wizard/custom-layout',
                        'redirect_url'    => '/foo',
                        'steps' => array(
                            'WizardTest\TestAsset\Step\Foo' => array(
                                'title'         => 'foo',
                                'view_template' => 'wizard/foo',
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
            )
        );
        
        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));
        $this->initializer
            ->expects($this->once())
            ->method('initialize');

        $wizardFactory = new WizardFactory($this->serviceLocator, $this->initializer);
        
        $wizard = $wizardFactory->create('foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
        $this->assertInstanceOf('WizardTest\TestAsset\Foo', $wizard);

        $this->assertEquals('wizard/custom-layout', $wizard->getOptions()->getLayoutTemplate());
        $this->assertEquals('/foo', $wizard->getOptions()->getRedirectUrl());

        $steps = $wizard->getSteps();
        $this->assertCount(3, $steps);

        $fooStep = $steps->get('foo');
        $this->assertEquals('foo', $fooStep->getTitle());
        $this->assertEquals('wizard/foo', $fooStep->getViewTemplate());

        $barStep = $steps->get('bar');
        $this->assertEquals('bar', $barStep->getTitle());
        $this->assertEquals('wizard/bar', $barStep->getViewTemplate());

        $bazStep = $steps->get('baz');
        $this->assertEquals('baz', $bazStep->getTitle());
        $this->assertEquals('wizard/baz', $bazStep->getViewTemplate());
    }

    /**
     * @expectedException \Wizard\Exception\RuntimeException
     */
    public function testCreateInvalidWizard()
    {
        $wizardFactory = new WizardFactory($this->serviceLocator, $this->initializer);
        $wizardFactory->create('invalid');
    }
}