<?php
namespace WizardTest;

use Wizard\WizardFactory;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

class WizardFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWizard()
    {
        $config = array(
            'default_class' => 'Wizard\Wizard',
            'default_layout_template' => 'wizard/layout',
            'wizards' => array(
                'Wizard\Foo' => array(
                    'class'           => 'WizardTest\TestAsset\Foo',
                    'layout_template' => 'wizard/custom-layout',
                    'redirect_url'    => '/foo',
                    'steps' => array(
                        'foo' => array(
                            'service'       => 'WizardTest\TestAsset\Step\Foo',
                            'title'         => 'foo',
                            'view_template' => 'wizard/foo',
                        ),
                        'bar' => array(
                            'service'       => 'WizardTest\TestAsset\Step\Bar',
                            'title'         => 'bar',
                            'view_template' => 'wizard/bar',
                        ),
                        'baz' => array(
                            'service'       => 'WizardTest\TestAsset\Step\Baz',
                            'title'         => 'baz',
                            'view_template' => 'wizard/baz',
                        ),
                    ),
                ),
            ),
        );

        $wizardFactory = new WizardFactory($config);

        $request = $this->getMock('Zend\Http\Request');
        $wizardFactory->setRequest($request);

        $response = $this->getMock('Zend\Http\Response');
        $wizardFactory->setResponse($response);

        $renderer = $this->getMock('Zend\View\Renderer\PhpRenderer');
        $wizardFactory->setRenderer($renderer);

        $formFactory = $this->getMock('Wizard\Form\FormFactory');
        $formFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue(new Form));
        $wizardFactory->setFormFactory($formFactory);

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('WizardTest\TestAsset\Step\Foo', $this->getMockForAbstractClass('Wizard\AbstractStep'))
            ->setService('WizardTest\TestAsset\Step\Bar', $this->getMockForAbstractClass('Wizard\AbstractStep'))
            ->setService('WizardTest\TestAsset\Step\Baz', $this->getMockForAbstractClass('Wizard\AbstractStep'));
        $wizardFactory->setServiceManager($serviceManager);
        
        $wizard = $wizardFactory->create('Wizard\Foo');
        $this->assertInstanceOf('Wizard\WizardInterface', $wizard);
        $this->assertInstanceOf('WizardTest\TestAsset\Foo', $wizard);

        $this->assertEquals('wizard/custom-layout', $wizard->getOptions()->getLayoutTemplate());
        $this->assertEquals('/foo', $wizard->getOptions()->getRedirectUrl());

        $steps = $wizard->getSteps();
        $this->assertCount(3, $steps);

        $fooStep = $steps->get('foo');
        $this->assertEquals('foo', $fooStep->getOptions()->getTitle());
        $this->assertEquals('wizard/foo', $fooStep->getOptions()->getViewTemplate());

        $barStep = $steps->get('bar');
        $this->assertEquals('bar', $barStep->getOptions()->getTitle());
        $this->assertEquals('wizard/bar', $barStep->getOptions()->getViewTemplate());

        $bazStep = $steps->get('baz');
        $this->assertEquals('baz', $bazStep->getOptions()->getTitle());
        $this->assertEquals('wizard/baz', $bazStep->getOptions()->getViewTemplate());
    }

    public function testCreateWizardWithDefaultOptions()
    {
        $config = array(
            'default_class' => 'Wizard\Wizard',
            'default_layout_template' => 'wizard/layout',
            'wizards' => array(
                'Wizard\Foo' => array(),
            ),
        );

        $wizardFactory = new WizardFactory($config);

        $request = $this->getMock('Zend\Http\Request');
        $wizardFactory->setRequest($request);

        $response = $this->getMock('Zend\Http\Response');
        $wizardFactory->setResponse($response);

        $renderer = $this->getMock('Zend\View\Renderer\PhpRenderer');
        $wizardFactory->setRenderer($renderer);

        $formFactory = $this->getMock('Wizard\Form\FormFactory');
        $formFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue(new Form));
        $wizardFactory->setFormFactory($formFactory);

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