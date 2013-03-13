<?php
namespace Wizard;

use Zend\Form\Form;

interface WizardInterface
{
    /**
     * @return StepInterface
     */
    public function getCurrentStep();

    /**
     * @return Form
     */
    public function getForm();

    /**
     * @param  StepCollection $steps
     * @return WizardInterface
     */
    public function setSteps(StepCollection $steps);

    /**
     * @return StepCollection
     */
    public function getSteps();
    
    /**
     * @return void
     */
    public function process();
}