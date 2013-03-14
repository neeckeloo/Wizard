<?php
namespace Wizard\Form\Element\Button;

use Zend\Form\Element\Button as BaseButton;

abstract class Submit extends BaseButton
{
    /**
     * @var array 
     */
    protected $attributes = array(
        'name' => 'submit',
        'type' => 'submit',
    );
}