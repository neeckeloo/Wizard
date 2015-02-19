<?php
namespace Wizard\Form\Element\Button;

use Zend\Form\Element\Button as BaseButton;

class Valid extends BaseButton
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'valid',
        'type' => 'submit',
    ];

    /**
     * @var string
     */
    protected $label = 'Valid';
}