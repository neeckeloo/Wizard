<?php
namespace Wizard;

use Zend\Stdlib\AbstractOptions;

class WizardOptions extends AbstractOptions implements WizardOptionsInterface
{
    /**
     * @var string
     */
    protected $tokenParamName = 'uid';

    /**
     * @var string
     */
    protected $layoutTemplate;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * {@inheritDoc}
     */
    public function getTokenParamName()
    {
        return $this->tokenParamName;
    }

    /**
     * {@inheritDoc}
     */
    public function setTokenParamName($name)
    {
        $this->tokenParamName = (string) $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
    }

    /**
     * {@inheritDoc}
     */
    public function setLayoutTemplate($template)
    {
        $this->layoutTemplate = (string) $template;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectUrl($url)
    {
        $this->redirectUrl = (string) $url;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setCancelUrl($url)
    {
        $this->cancelUrl = (string) $url;
        return $this;
    }
}
