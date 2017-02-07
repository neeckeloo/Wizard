<?php
namespace Wizard;

use Zend\Http\Request as HttpRequest;
use Zend\Router\RouteInterface;
use Zend\Stdlib\RequestInterface;

class WizardResolver
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RouteInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param RequestInterface $request
     * @param RouteInterface $router
     * @param array $config
     */
    public function __construct(RequestInterface $request, RouteInterface $router, array $config)
    {
        $this->request = $request;
        $this->router  = $router;
        $this->config  = $config;
    }

    /**
     * @return string|null
     */
    public function resolve()
    {
        if (!$this->request instanceof HttpRequest) {
            return;
        }

        $routeMatch = $this->router->match($this->request);
        if (!$routeMatch) {
            return;
        }

        $matchedRouteName = $routeMatch->getMatchedRouteName();

        foreach ($this->config['wizards'] as $name => $options) {
            if (empty($options['route'])) {
                continue;
            }

            if (is_string($options['route'])) {
                $options['route'] = [$options['route']];
            }

            if (!in_array($matchedRouteName, $options['route'])) {
                continue;
            }

            return $name;
        }
    }
}
