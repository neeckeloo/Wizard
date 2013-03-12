<?php
namespace Wizard;

interface WizardInterface
{
    /**
     * @param StepInterface $step
     */
    public function process(StepInterface $step);
}