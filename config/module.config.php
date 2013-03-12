<?php
return array(
    'wizard' => array(
        'application_name' => null,
        'license' => null,
        'browser_timing_enabled' => false,
        'browser_timing_auto_instrument' => true,
        'exceptions_logging_enabled' => false,
    ),
    'service_manager' => array(
        'factories' => array(
            'wizard'     => 'Wizard\Service\WizardFactory',
        ),
    ),
);