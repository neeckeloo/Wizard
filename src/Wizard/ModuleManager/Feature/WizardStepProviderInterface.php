<?php
namespace Wizard\ModuleManager\Feature;

interface WizardStepProviderInterface
{
    /**
     * @return array|\Zend\ServiceManager\Config
     */
    public function getWizardStepConfig();
}
