Wizard module for Zend Framework 2
==================================

[![Build Status](https://secure.travis-ci.org/neeckeloo/Wizard.png?branch=master)](http://travis-ci.org/neeckeloo/Wizard)
[![Coverage Status](https://coveralls.io/repos/neeckeloo/Wizard/badge.png?branch=master)](https://coveralls.io/r/neeckeloo/Wizard)

Introduction
------------

Wizard module intend to provide a way to manage multi-step forms. For that, wizard contains all the steps which each has its own form and data validation. We use session to temporarily store step data and finally store them into a database (for instance) at the end of the wizard.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master).

Features / Goals
----------------

* Manage wizard process [COMPLETE]

Installation
------------

1. Add this project in your composer.json:

    ```json
    "require": {
        "neeckeloo/wizard": "dev-master"
    }
    ```

2. Now tell composer to download dependencies by running the command:

    ```bash
    $ php composer.phar update
    ```

3. Enabling it in your `application.config.php` file:

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

4. Copy the `./vendors/neeckeloo/wizard/config/wizard.global.php.dist` to your `./config/autoload/wizard.global.php`.

        <?php

        $wizard = array(
            'default_layout_template' => 'wizard/layout',
            'default_class' => 'Wizard\Wizard',
            'wizards' => array(),
        );

        return array('wizard' => $wizard);