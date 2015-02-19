<?php
namespace Wizard\Form\Element\Button;

use Zend\Form\Element\Button as BaseButton;

class Previous extends BaseButton
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'previous',
        'type' => 'submit',
    ];

    /**
     * @var string
     */
    protected $label = 'Previous';
}