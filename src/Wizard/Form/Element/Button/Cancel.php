<?php
namespace Wizard\Form\Element\Button;

use Zend\Form\Element\Button as BaseButton;

class Cancel extends BaseButton
{
    /**
     * @var array
     */
    protected $attributes = array(
        'name' => 'cancel',
        'type' => 'submit',
    );

    /**
     * @var string
     */
    protected $label = 'Cancel';
}