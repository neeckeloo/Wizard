<?php
return [
    'wizard' => [
        'default_layout_template' => 'wizard/layout',
    ],

    'service_manager' => [
        'invokables' => [
            'Wizard\Form\FormFactory'                => 'Wizard\Form\FormFactory',
            'Wizard\Listener\StepCollectionListener' => 'Wizard\Listener\StepCollectionListener',
            'Wizard\Listener\WizardListener'         => 'Wizard\Listener\WizardListener',
        ],
        'factories' => [
            'Wizard\Config'                    => 'Wizard\Factory\ConfigFactory',
            'Wizard\Step\StepFactory'          => 'Wizard\Factory\StepFactoryFactory',
            'Wizard\WizardFactory'             => 'Wizard\Factory\WizardFactoryFactory',
            'Wizard\Listener\DispatchListener' => 'Wizard\Factory\DispatchListenerFactory',
            'Wizard\Wizard'                    => 'Wizard\Factory\WizardFactory',
            'Wizard\WizardRenderer'            => 'Wizard\Factory\WizardRendererFactory',
            'Wizard\WizardResolver'            => 'Wizard\Factory\WizardResolverFactory',
            'Wizard\Step\StepPluginManager'    => 'Wizard\Factory\StepPluginManagerFactory',
        ],
        'shared' => [
            'Wizard\Wizard' => false,
        ],
    ],

    'form_elements' => [
        'invokables' => [
            'Wizard\Form\Element\Button\Cancel'   => 'Wizard\Form\Element\Button\Cancel',
            'Wizard\Form\Element\Button\Next'     => 'Wizard\Form\Element\Button\Next',
            'Wizard\Form\Element\Button\Previous' => 'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Valid'    => 'Wizard\Form\Element\Button\Valid',
        ],
    ],

    'view_manager' => [
        'template_map' => [
            'wizard/layout'  => __DIR__ . '/../view/wizard/layout.phtml',
            'wizard/header'  => __DIR__ . '/../view/wizard/header.phtml',
            'wizard/buttons' => __DIR__ . '/../view/wizard/buttons.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
