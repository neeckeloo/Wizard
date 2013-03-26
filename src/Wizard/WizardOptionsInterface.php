<?php
namespace Wizard;

interface WizardOptionsInterface
{
    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param  string $url
     * @return WizardOptionsInterface
     */
    public function setRedirectUrl($url);

    /**
     * @return string
     */
    public function getLayoutTemplate();

    /**
     * @param  string $template
     * @return WizardOptionsInterface
     */
    public function setLayoutTemplate($template);
}