<?php
namespace Wizard;

use Zend\Mvc\Router\RouteMatch;

class WizardResolver
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param RouteMatch $routeMatch
     * @param array $config
     */
    public function __construct(RouteMatch $routeMatch, array $config)
    {
        $this->routeMatch = $routeMatch;
        $this->config     = $config;
    }

    /**
     * @return string|null
     */
    public function resolve()
    {
        $matchedRouteName = $this->routeMatch->getMatchedRouteName();

        foreach ($this->config['wizards'] as $name => $options) {
            if (empty($options['route'])) {
                continue;
            }

            if (is_string($options['route'])) {
                $options['route'] = array($options['route']);
            }

            if (!in_array($matchedRouteName, $options['route'])) {
                continue;
            }

            return $name;
        }

        return null;
    }
}
