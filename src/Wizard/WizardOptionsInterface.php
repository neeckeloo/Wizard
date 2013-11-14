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
    public function getCancelUrl();

    /**
     * @param  string $url
     * @return WizardOptionsInterface
     */
    public function setCancelUrl($url);

    /**
     * @return string
     */
    public function getLayoutTemplate();

    /**
     * @param  string $template
     * @return WizardOptionsInterface
     */
    public function setLayoutTemplate($template);
    
    /**
     * @param  string $name
     * @return WizardOptionsInterface
     */
    public function setName($name);
    
    /**
     * @return string
     */
    public function getName();
}