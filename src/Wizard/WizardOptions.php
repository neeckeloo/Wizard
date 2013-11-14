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
    protected $cancelUrl;

    /**
     * @var string
     */
    protected $layoutTemplate;

    /**
     * @var string
     */
    protected $name;
    
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
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
}