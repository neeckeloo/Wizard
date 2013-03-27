<?php
namespace Wizard;

use Zend\Stdlib\AbstractOptions;

class WizardOptions extends AbstractOptions implements WizardOptionsInterface
{
    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $layoutTemplate;

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
}
