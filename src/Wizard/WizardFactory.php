<?php
namespace Wizard;

use Wizard\Form\FormFactory;
use Wizard\Listener\StepCollectionListener;
use Wizard\Listener\WizardListener;
use Zend\Http\Request;
use Zend\Http\Response;
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
     * @var Request
     */
    protected $request;

    /**
     * @var Response
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
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
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

        if (isset($config['layout_template'])) {
            $layoutTemplate = $config['layout_template'];
        } else {
            $layoutTemplate = $this->config['default_layout_template'];
        }
        $wizard->getOptions()->setLayoutTemplate($layoutTemplate);
        
        if (isset($config['redirect_url'])) {
            $wizard->getOptions()->setRedirectUrl($config['redirect_url']);
        }

        if (isset($config['steps']) && is_array($config['steps'])) {
            $this->addSteps($config['steps'], $wizard);
        }
        
        $wizardListener = new WizardListener();
        $wizard->getEventManager()->attachAggregate($wizardListener);

        $stepListener = new StepCollectionListener();
        $stepCollection = $wizard->getSteps();
        $stepCollection->getEventManager()->attachAggregate($stepListener);

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
     * @param  string $name
     * @param  array $options
     * @return StepInterface
     */
    protected function createStep($service, $options)
    {
        /* @var $step \Wizard\StepInterface */
        $step = $this->serviceManager->get($service);

        $step->setName($service);
        $step->getOptions()->setFromArray($options);

        return $step;
    }
}