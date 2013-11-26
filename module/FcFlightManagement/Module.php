<?php
/**
 * @namespace
 */
namespace FcFlightManagement;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcFlight\Model\FlightHeaderModel;
use FcFlightManagement\Filter\RefuelStep1Filter;

/**
 * Class Module
 * @package FcFlightManagement
 */
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

    /**
     * @return array
     */
    public function getValidatorConfig()
    {
        return array(
            'invokables' => array(),
        );
    }

    /**
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', array($this, 'onModuleDispatch'));
    }

    /**
     * @param MvcEvent $e
     */
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
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'LogPlugin' => function ($sm) {
                        $serviceLocator = $sm->getServiceLocator();
                        $authService = $serviceLocator->get('zfcuser_auth_service');
                        $controllerPlugin = new Controller\Plugin\LogPlugin;
                        $controllerPlugin->setCurrentUserName($authService->getIdentity()->getUsername());
                        return $controllerPlugin;
                    },
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(),
            'factories' => array(
                'FcFlight\Model\FlightHeaderModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new FlightHeaderModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\RefuelStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelStep1Filter($dbAdapter);
                    },
            ),
        );
    }
}

