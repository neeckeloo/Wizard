<?php
return array(
    'wizard' => array(
        'default_layout_template' => 'wizard/layout',
    ),

    'service_manager' => array(
        'invokables' => array(
            'Wizard\Form\FormFactory' => 'Wizard\Form\FormFactory',
        ),
        'factories' => array(
            'Wizard\Config'                    => 'Wizard\Factory\ConfigFactory',
            'Wizard\WizardFactory'             => 'Wizard\Factory\WizardFactoryFactory',
            'Wizard\Listener\DispatchListener' => 'Wizard\Factory\DispatchListenerFactory',
            'Wizard\Wizard'                    => 'Wizard\Factory\WizardFactory',
            'Wizard\WizardRenderer'            => 'Wizard\Factory\WizardRendererFactory',
            'Wizard\WizardResolver'            => 'Wizard\Factory\WizardResolverFactory',
            'Wizard\Step\StepPluginManager'    => 'Wizard\Factory\StepPluginManagerFactory',
        ),
        'shared' => array(
            'Wizard\Wizard' => false,
        ),
    ),

    'form_elements' => array(
        'invokables' => array(
            'Wizard\Form\Element\Button\Cancel'   => 'Wizard\Form\Element\Button\Cancel',
            'Wizard\Form\Element\Button\Next'     => 'Wizard\Form\Element\Button\Next',
            'Wizard\Form\Element\Button\Previous' => 'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Valid'    => 'Wizard\Form\Element\Button\Valid',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'wizard/layout'  => __DIR__ . '/../view/wizard/layout.phtml',
            'wizard/header'  => __DIR__ . '/../view/wizard/header.phtml',
            'wizard/buttons' => __DIR__ . '/../view/wizard/buttons.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);