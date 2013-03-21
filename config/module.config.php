<?php
return array(
    'wizard' => array(
        
    ),
    'service_manager' => array(
        'factories' => array(
            'Wizard\Form'           => 'Wizard\Service\FormFactory',
            'Wizard\WizardRenderer' => 'Wizard\Service\WizardRendererFactory',
        ),
        'invokables' => array(
            'Zend\Session\Storage'                => 'Zend\Session\Storage\SessionStorage',
            'Wizard\Form\Element\Button\Previous' => 'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Next'     => 'Wizard\Form\Element\Button\Next',
            'Wizard\Form\Element\Button\Valid'    => 'Wizard\Form\Element\Button\Valid',
        ),
        'aliases' => array(
            'session' => 'Zend\Session\Storage',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'layout'        => __DIR__ . '/../view/wizard/layout.phtml',
            'wizard/header' => __DIR__ . '/../view/wizard/header.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);