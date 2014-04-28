<?php
namespace Wizard;

use Wizard\WizardInterface;
use Zend\EventManager\Event;

class WizardEvent extends Event
{
    const EVENT_INIT = 'wizard-init';
    const EVENT_COMPLETE = 'wizard-complete';
    const EVENT_PRE_PROCESS_STEP = 'step-pre-process';
    const EVENT_POST_PROCESS_STEP = 'step-post-process';

    /**
     * @var WizardInterface
     */
    protected $wizard;

    /**
     * @param  WizardInterface $wizard
     * @return self
     */
    public function setWizard(WizardInterface $wizard)
    {
        $this->setParam('wizard', $wizard);
        $this->wizard = $wizard;
        return $this;
    }

    /**
     * @return WizardInterface
     */
    public function getWizard()
    {
        return $this->wizard;
    }
}
