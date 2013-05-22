<?php
namespace WizardTest;

use Wizard\WizardFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');
    }

    public function testCreateWizard()
    {
        $config = array(
            'wizard' => array(
                'default_class' => 'Wizard\Wizard',
                'default_layout_template' => 'wizard/layout',
                'wizards' => array(
                    'Wizard\Foo' => array(
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
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($config));

        $application = $this->getMock('\Zend\Mvc\Application', array(), array(), '', false);
        $application
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Zend\Http\Request')));
        $application
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->getMock('Zend\Http\Response')));

        $this->serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($application));

        $sessionManager = $this->getMock('Zend\Session\SessionManager');
        $this->serviceLocator
            ->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue($sessionManager));

        $renderer = $this->getMock('Zend\View\Renderer\PhpRenderer');
        $this->serviceLocator
            ->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue($renderer));

        $wizardFactory = new WizardFactory($this->serviceLocator);
        
        $wizard = $wizardFactory->create('Wizard\Foo');
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

    public function testCreateWizardWithDefaultOptions()
    {
        $config = array(
            'wizard' => array(
                'default_class' => 'Wizard\Wizard',
                'default_layout_template' => 'wizard/layout',
                'wizards' => array(
                    'Wizard\Foo' => array(),
                ),
            )
        );

        $this->serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($config));

        $application = $this->getMock('\Zend\Mvc\Application', array(), array(), '', false);
        $application
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Zend\Http\Request')));
        $application
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->getMock('Zend\Http\Response')));

        $this->serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($application));


        $sessionManager = $this->getMock('Zend\Session\SessionManager');
        $this->serviceLocator
            ->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue($sessionManager));

        $renderer = $this->getMock('Zend\View\Renderer\PhpRenderer');
        $this->serviceLocator
            ->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue($renderer));

        $wizardFactory = new WizardFactory($this->serviceLocator);

        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
        $this->assertEquals('wizard/layout', $wizard->getOptions()->getLayoutTemplate());
    }

    /**
     * @expectedException \Wizard\Exception\RuntimeException
     */
    public function testCreateInvalidWizard()
    {
        $wizardFactory = new WizardFactory($this->serviceLocator);
        $wizardFactory->create('invalid');
    }
}