<?php
namespace Wizard\Step;

interface StepOptionsInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getViewTemplate();

    /**
     * @param  string $template
     * @return self
     */
    public function setViewTemplate($template);
}