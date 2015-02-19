<?php
namespace Wizard\Step;

use Wizard\Step\StepPluginManager;
use Zend\Form\FormElementManager;

class StepFactory
{
    /**
     * @var StepPluginManager
     */
    protected $stepPluginManager;

    /**
     * @var FormElementManager
     */
    protected $formPluginManager;

    /**
     * @param StepPluginManager $stepPluginManager
     * @param FormElementManager $formPluginManager
     */
    public function __construct(
        StepPluginManager $stepPluginManager,
        FormElementManager $formPluginManager
    ) {
        $this->stepPluginManager = $stepPluginManager;
        $this->formPluginManager = $formPluginManager;
    }

    /**
     * @param  string $name
     * @param  array $options
     * @return \Wizard\Step\StepInterface
     */
    public function create($name, array $options = [])
    {
        /* @var $step \Wizard\StepInterface */
        $step = $this->stepPluginManager->get($name);

        if (isset($options['form'])) {
            $form = $this->formPluginManager->get($options['form']);
            $step->setForm($form);
            unset($options['form']);
        }

        $step->setName($name);
        $step->getOptions()->setFromArray($options);

        return $step;
    }
}
