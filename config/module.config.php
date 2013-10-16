<?php
return array(
    'wizard' => array(
        'default_layout_template' => 'wizard/layout',
        'default_class'           => 'Wizard\Wizard',
    ),
    'service_manager' => array(
        'factories' => array(
            'Wizard\Config'         => 'Wizard\Factory\ConfigFactory',
            'Wizard\Factory'        => 'Wizard\Factory\WizardFactoryFactory',
            'Wizard\WizardRenderer' => 'Wizard\Factory\WizardRendererFactory',
        ),
        'invokables' => array(
            'Wizard\Form\Element\Button\Cancel'   => 'Wizard\Form\Element\Button\Cancel',
            'Wizard\Form\Element\Button\Next'     => 'Wizard\Form\Element\Button\Next',
            'Wizard\Form\Element\Button\Previous' => 'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Valid'    => 'Wizard\Form\Element\Button\Valid',
            'Wizard\Form\FormFactory'             => 'Wizard\Form\FormFactory',
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