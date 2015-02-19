<?php
namespace Wizard;

use Wizard\Step\StepFactory;
use Wizard\WizardInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class WizardFactory implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var StepFactory
     */
    protected $stepFactory;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $instances = [];

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
     * @param StepFactory $factory
     */
    public function setStepFactory(StepFactory $factory)
    {
        $this->stepFactory = $factory;
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
        $wizard = $this->serviceManager->get('Wizard\Wizard');

        if (empty($config['layout_template'])) {
            $config['layout_template'] = $this->config['default_layout_template'];
        }

        $wizard->getOptions()->setFromArray($config);

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
            $step = $this->stepFactory->create($key, $values);
            if (!$step) {
                continue;
            }

            $step
                ->setWizard($wizard)
                ->init();

            $wizard->getSteps()->add($step);
        }
    }
}
