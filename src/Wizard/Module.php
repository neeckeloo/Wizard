<?php
namespace Wizard;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleManager;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
    public function init(ModuleManager $moduleManager)
    {
        $serviceManager = $moduleManager->getEvent()->getParam('ServiceManager');

        /* @var $serviceListener \Zend\ModuleManager\Listener\ServiceListenerInterface */
        $serviceListener = $serviceManager->get('ServiceListener');

        $serviceListener->addServiceManager(
            'WizardStepManager',
            'wizard_steps',
            'Wizard\ModuleManager\Feature\WizardStepProviderInterface',
            'getWizardStepConfig'
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
