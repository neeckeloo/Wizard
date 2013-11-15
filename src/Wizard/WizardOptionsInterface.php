<?php
namespace Wizard;

interface WizardOptionsInterface
{
    /**
     * @return string
     */
    public function getTokenParamName();

    /**
     * @param  string $name
     * @return self
     */
    public function setTokenParamName($name);

    /**
     * @return string
     */
    public function getLayoutTemplate();

    /**
     * @param  string $template
     * @return self
     */
    public function setLayoutTemplate($template);

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param  string $url
     * @return self
     */
    public function setRedirectUrl($url);

    /**
     * @return string
     */
    public function getCancelUrl();

    /**
     * @param  string $url
     * @return self
     */
    public function setCancelUrl($url);
}