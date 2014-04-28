<?php
namespace Wizard;

use Wizard\Form\FormFactory;
use Wizard\Listener\StepCollectionListener;
use Wizard\Listener\WizardListener;
use Wizard\Step\StepInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class WizardFactory implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var array
     */
    protected $config = array();
    
    /**
     * @var array
     */
    protected $instances = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = (array) $config;
    }

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param HttpRequest $request
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param HttpResponse $response
     */
    public function setResponse(HttpResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @param FormFactory $factory
     */
    public function setFormFactory(FormFactory $factory)
    {
        $this->formFactory = $factory;
    }

    /**
     * @param  string $name
     * @return WizardInterface
     */
    public function create($name)
    {        
        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }
        
        if (!isset($this->config['wizards'][$name])) {
            throw new Exception\RuntimeException(sprintf(
                'The wizard "%s" does not exists.',
                $name
            ));
        }

        $config = $this->config['wizards'][$name];

        /* @var $wizard \Wizard\WizardInterface */
        $wizard = new Wizard();

        $wizard
            ->setRequest($this->request)
            ->setResponse($this->response)
            ->setFormFactory($this->formFactory);
        
        $wizardListener = new WizardListener();
        $wizard->getEventManager()->attachAggregate($wizardListener);

        $stepListener = new StepCollectionListener();
        $stepCollection = $wizard->getSteps();
        $stepCollection->getEventManager()->attachAggregate($stepListener);
        
        $wizardOptions = $wizard->getOptions();
        
        if (isset($config['title'])) {
            $wizardOptions->setTitle($config['title']);
        }
        
        if (isset($config['layout_template'])) {
            $layoutTemplate = $config['layout_template'];
        } else {
            $layoutTemplate = $this->config['default_layout_template'];
        }
        $wizardOptions->setLayoutTemplate($layoutTemplate);
        
        if (isset($config['redirect_url'])) {
            $wizardOptions->setRedirectUrl($config['redirect_url']);
        }

        if (isset($config['steps']) && is_array($config['steps'])) {
            $this->addSteps($config['steps'], $wizard);
        }
        
        if (isset($config['listeners']) && is_array($config['listeners'])) {
            foreach ($config['listeners'] as $listener) {
                $instance = $this->serviceManager->get($listener);
                $wizard->getEventManager()->attach($instance);
            }
        }
        
        $wizard->init();
        
        $this->instances[$name] = $wizard;

        return $wizard;
    }

    /**
     * @param array $steps
     * @param Wizard $wizard
     */
    protected function addSteps(array $steps, Wizard $wizard)
    {
        foreach ($steps as $key => $values) {
            $step = $this->createStep($key, $values);
            if (!$step) {
                continue;
            }

            $step
                ->setWizard($wizard)
                ->init();

            $wizard->getSteps()->add($step);
        }
    }

    /**
     * @param  string $service
     * @param  array $options
     * @return StepInterface
     */
    protected function createStep($service, $options)
    {
        $stepPluginManager = $this->serviceManager->get('Wizard\Step\StepPluginManager');
        
        /* @var $step \Wizard\StepInterface */
        $step = $stepPluginManager->get($service);

        if (isset($options['form'])) {
            $formManager = $this->serviceManager->get('FormElementManager');
            $form = $formManager->get($options['form']);
            $step->setForm($form);
            unset($options['form']);
        }

        $step->setName($service);
        $step->getOptions()->setFromArray($options);

        return $step;
    }
}
