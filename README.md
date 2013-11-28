Wizard module for Zend Framework 2
==================================

[![Build Status](https://secure.travis-ci.org/neeckeloo/Wizard.png?branch=master)](http://travis-ci.org/neeckeloo/Wizard)
[![Coverage Status](https://coveralls.io/repos/neeckeloo/Wizard/badge.png?branch=master)](https://coveralls.io/r/neeckeloo/Wizard)
[![Dependencies Status](https://d2xishtp1ojlk0.cloudfront.net/d/8723804)](http://depending.in/neeckeloo/Wizard)

Introduction
------------

Wizard module intend to provide a way to manage multi-step forms. For that, wizard contains all the steps which each has its own form and data validation. We use session to temporarily store step data and finally store them into a database (for instance) at the end of the wizard.

Requirements
------------

* PHP 5.3 or higher
* [Zend Framework 2](https://github.com/zendframework/zf2)

Installation
------------

Installation of Wizard uses composer. For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).

#### Installation steps

1. Add this in your composer.json:

```json
"require": {
    "neeckeloo/wizard": "dev-master"
}
```

2. Download package by running command:

```bash
$ php composer.phar update
```

3. Enabling it in your `config/application.config.php` file:

```php
<?php
return array(
    'modules' => array(
        // ...
        'Wizard'
    ),
    // ...
);
```

Sample configuration
--------------------

Add this code in a `wizard.global.php` file into your `config/autoload` directory.

```php
<?php
return array(
    'wizard' => array(
        'default_layout_template' => 'wizard/layout',
        'wizards' => array(
            'Wizard\Foo' => array(
                'class'           => 'WizardTest\TestAsset\Foo',
                'layout_template' => 'wizard/custom-layout',
                'redirect_url'    => '/foo',
                'cancel_url'      => '/bar',
                'steps' => array(
                    'foo' => array(
                        'service'       => 'WizardTest\TestAsset\Step\Foo',
                        'title'         => 'foo',
                        'view_template' => 'wizard/foo',
                    ),
                    'bar' => array(
                        'service'       => 'WizardTest\TestAsset\Step\Bar',
                        'title'         => 'bar',
                        'view_template' => 'wizard/bar',
                    ),
                    'baz' => array(
                        'service'       => 'WizardTest\TestAsset\Step\Baz',
                        'title'         => 'baz',
                        'view_template' => 'wizard/baz',
                    ),
                ),
            ),
        ),
    ),
);
```
