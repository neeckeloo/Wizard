<?php
return array(
    'wizard' => array(
        
    ),
    'service_manager' => array(
        'factories' => array(
            'wizard' => 'Wizard\Service\WizardFactory',
        ),
        'invokables' => array(
            'session' => 'Zend\Session\Storage\SessionArrayStorage',
        ),
    ),
);