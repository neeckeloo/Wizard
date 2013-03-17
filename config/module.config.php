<?php
return array(
    'wizard' => array(
        
    ),
    'service_manager' => array(
        'factories' => array(
            'Wizard\Form' => 'Wizard\Service\FormFactory',
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
);