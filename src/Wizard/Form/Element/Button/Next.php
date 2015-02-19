<?php
namespace Wizard\Form\Element\Button;

use Zend\Form\Element\Button as BaseButton;

class Next extends BaseButton
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'next',
        'type' => 'submit',
    ];

    /**
     * @var string
     */
    protected $label = 'Next';
}