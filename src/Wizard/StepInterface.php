<?php
namespace Wizard;

interface StepInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param  string $title
     * @return StepInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return void
     */
    public function process();

    /**
     * @param  array $data
     * @return bool
     */
    public function isValid($data);
}