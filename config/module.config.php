<?php
use Wizard\Listener\StepCollectionListener;
use Wizard\Listener\WizardListener;
use Wizard\Factory\ConfigFactory;
use Wizard\Step\StepFactory;
use Wizard\Factory\StepFactoryFactory;
use Wizard\Factory\WizardFactoryFactory;
use Wizard\Listener\DispatchListener;
use Wizard\Factory\DispatchListenerFactory;
use Wizard\Factory\WizardFactory;
use Wizard\WizardProcessor;
use Wizard\Factory\WizardProcessorFactory;
use Wizard\WizardResolver;
use Wizard\Factory\WizardResolverFactory;
use Wizard\Wizard\IdentifierAccessor;
use Wizard\Factory\IdentifierAccessorFactory;
use Wizard\Step\StepPluginManager;
use Wizard\Factory\StepPluginManagerFactory;
use Wizard\Form\FormFactory;
use Wizard\Factory\FormFactoryFactory;
use Wizard\Wizard;
use Wizard\Form\Element\Button\Cancel;
use Wizard\Form\Element\Button\Next;
use Wizard\Form\Element\Button\Previous;
use Wizard\Form\Element\Button\Valid;

return [
    'wizard' => [
        'default_layout_template' => 'wizard/layout',
    ],

    'service_manager' => [
        'invokables' => [
            StepCollectionListener::class => StepCollectionListener::class,
            WizardListener::class => WizardListener::class,
        ],
        'factories' => [
            'Wizard\Config' => ConfigFactory::class,
            StepFactory::class => StepFactoryFactory::class,
            \Wizard\WizardFactory::class => WizardFactoryFactory::class,
            DispatchListener::class => DispatchListenerFactory::class,
            Wizard::class => WizardFactory::class,
            WizardProcessor::class => WizardProcessorFactory::class,
            WizardResolver::class => WizardResolverFactory::class,
            IdentifierAccessor::class => IdentifierAccessorFactory::class,
            StepPluginManager::class => StepPluginManagerFactory::class,
            FormFactory::class => FormFactoryFactory::class,
        ],
        'shared' => [
            Wizard::class => false,
        ],
    ],

    'form_elements' => [
        'invokables' => [
            Cancel::class => Cancel::class,
            Next::class => Next::class,
            Previous::class => Previous::class,
            Valid::class => Valid::class,
        ],
    ],

    'view_manager' => [
        'template_map' => [
            'wizard/layout' => __DIR__ . '/../view/wizard/layout.phtml',
            'wizard/header' => __DIR__ . '/../view/wizard/header.phtml',
            'wizard/buttons' => __DIR__ . '/../view/wizard/buttons.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
