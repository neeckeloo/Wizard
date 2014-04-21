Wizard module for Zend Framework 2
==================================

[![Build Status](https://secure.travis-ci.org/neeckeloo/Wizard.png?branch=master)](http://travis-ci.org/neeckeloo/Wizard)
[![Latest Stable Version](https://poser.pugx.org/neeckeloo/Wizard/v/stable.png)](https://packagist.org/packages/neeckeloo/Wizard)
[![Coverage Status](https://coveralls.io/repos/neeckeloo/Wizard/badge.png?branch=master)](https://coveralls.io/r/neeckeloo/Wizard)
[![Dependencies Status](http://depending.in/neeckeloo/Wizard.png)](http://depending.in/neeckeloo/Wizard)

Introduction
------------

Wizard module intend to provide a way to manage multi-step forms. For that, wizard contains all the steps which each has its own form and data validation. We use session to temporarily store step data and finally store them into a database (for instance) at the end of the wizard.

Requirements
------------

* PHP 5.3 or higher
* [Zend Framework 2](https://github.com/zendframework/zf2)

Installation
------------

Wizard module only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Install the module:

```sh
$ php composer.phar require neeckeloo/wizard:~1.0
```

Enable the module by adding `Wizard` key to your `application.config.php` file. Customize the module by copy-pasting
the `wizard.global.php.dist` file to your `config/autoload` folder.

## Documentation

The official documentation is available in the [/docs](/docs) folder.
