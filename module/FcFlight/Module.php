<?php
namespace FcFlight;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcFlight\Model\FlightModel;
use FcFlight\Filter\FlightHeaderFilter;

class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

     public function init(ModuleManager $moduleManager)
     {
         $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
         $sharedEvents->attach(__NAMESPACE__, 'dispatch', array($this, 'onModuleDispatch'));
     }

     public function onModuleDispatch(MvcEvent $e)
     {
         //Set the layout template for every action in this module
         $controller = $e->getTarget();
         $controller->layout('layout/layout');

         //Set the main menu into the layout view model
         $serviceManager = $e->getApplication()->getServiceManager();
         $navBarContainer = $serviceManager->get('fcontrol_navigation');

         $viewModel = $e->getViewModel();
         $viewModel->setVariable('navBar', $navBarContainer);
     }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(),
            'factories' => array(
                'FcFlight\Model\FlightModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new FlightModel($dbAdapter);
                },
                'FcFlight\Filter\FlightHeaderFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new FlightHeaderFilter($dbAdapter);
                },
            ),
        );
    }
}

