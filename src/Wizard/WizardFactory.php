<?php
namespace Wizard;

use Zend\ServiceManager\ServiceLocatorInterface;

class WizardFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config = $this->serviceLocator->get('Config');
    }

    /**
     * @param  string $name
     * @return WizardInterface
     */
    public function create($name)
    {
        if (!isset($this->config['wizard']['wizards'][$name])) {
            throw new Exception\RuntimeException(sprintf(
                'The wizard "%s" does not exists.',
                $name
            ));
        }

        $config = $this->config['wizard']['wizards'][$name];

        if (isset($config['class']) && class_exists($config['class'])) {
            $class = $config['class'];
        } else {
            $class = $this->config['wizard']['default_class'];
        }

        /* @var $wizard \Wizard\WizardInterface */
        $wizard = new $class();

        $application = $this->serviceLocator->get('Application');

        $request = $application->getRequest();
        $response = $application->getResponse();

        $sessionManager = $this->serviceLocator->get('Session\Manager');

        $wizard
            ->setServiceManager($this->serviceLocator)
            ->setRequest($request)
            ->setResponse($response)
            ->setSessionManager($sessionManager);

        $renderer = $this->serviceLocator->get('Wizard\WizardRenderer');
        $wizard->setRenderer($renderer);

        if (isset($config['layout_template'])) {
            $layoutTemplate = $config['layout_template'];
        } else {
            $layoutTemplate = $this->config['wizard']['default_layout_template'];
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

                $step
                    ->setWizard($wizard)
                    ->init();

                $wizard->getSteps()->add($step);
            }
        }

        return $wizard;
    }
}