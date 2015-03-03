<?php
namespace Wizard\Wizard;

use Zend\Http\Request as HttpRequest;

class IdentifierAccessor
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @param  HttpRequest $request
     */
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getIdentifier($paramName)
    {
        $tokenValue = $this->request->getQuery($paramName, false);

        if ($tokenValue) {
            return $tokenValue;
        }

        return md5(uniqid(rand(), true));
    }
}
