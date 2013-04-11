<?php
namespace Wizard;

use Wizard\Service\WizardInitializer;
use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var WizardInitializer
     */
    protected $initializer;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param WizardInitializer $initializer
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, WizardInitializer $initializer)
    {
        $this->serviceLocator = $serviceLocator;

        $config = $this->serviceLocator->get('Config');
        if (isset($config['wizard'])) {
            $this->config = $config['wizard'];
        }

        $this->initializer = $initializer;
    }

    /**
     * @param  string $name
     * @return WizardInterface
     */
    public function create($name)
    {
        if (!isset($this->config['wizards'][$name])) {
            throw new Exception\RuntimeException(sprintf(
                'The wizard "%s" does not exists.',
                $name
            ));
        }

        $config = $this->config['wizards'][$name];

        if (isset($config['class']) && class_exists($config['class'])) {
            $class = $config['class'];
        } else {
            $class = $this->config['default_class'];
        }

        /* @var $wizard \Wizard\WizardInterface */
        $wizard = new $class();
        $this->initializer->initialize($wizard, $this->serviceLocator);

        if (isset($config['layout_template'])) {
            $layoutTemplate = $config['layout_template'];
        } else {
            $layoutTemplate = $this->config['default_layout_template'];
        }
        $wizard->getOptions()->setLayoutTemplate($layoutTemplate);
        
        if (isset($config['redirect_url'])) {
            $wizard->getOptions()->setRedirectUrl($config['redirect_url']);
        }

        if (isset($config['steps'])) {
            foreach ($config['steps'] as $key => $values) {
                if (!class_exists($key)) {
                    continue;
                }

                /* @var $step \Wizard\StepInterface */
                $step = new $key();

                if (isset($values['title'])) {
                    $step->setTitle($values['title']);
                }
                if (isset($values['view_template'])) {
                    $step->setViewTemplate($values['view_template']);
                }

                $wizard->getSteps()->add($step);
            }
        }

        return $wizard;
    }
}