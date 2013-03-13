<?php
namespace Wizard\Service;

use Wizard\Wizard;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SessionManager;

class WizardFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Wizard
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        //$configuration = new Configuration($config['wizard']);

        $application = $serviceLocator->get('Application');

        $request = $application->getRequest();
        $response = $application->getResponse();
        $routeMatch = $application->getMvcEvent()->getRouteMatch();

        $sessionStorage = $serviceLocator->get('session');
        $sessionManager = new SessionManager(null, $sessionStorage);

        return new Wizard($request, $response, $routeMatch, $sessionManager);
    }
}