<?php
return array(
    'wizard' => array(
        
    ),
    'service_manager' => array(
        'factories' => array(
            'Wizard\Form' => 'Wizard\Service\FormFactory',
        ),
        'invokables' => array(
            'Zend\Session\Storage'                => 'Zend\Session\Storage\SessionArrayStorage',
            'Wizard\Form\Element\Button\Previous' => 'Wizard\Form\Element\Button\Previous',
            'Wizard\Form\Element\Button\Next'     => 'Wizard\Form\Element\Button\Next',
        ),
        'aliases' => array(
            'session' => 'Zend\Session\Storage',
        ),
    ),
);