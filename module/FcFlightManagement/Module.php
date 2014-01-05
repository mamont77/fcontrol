<?php
/**
 * @namespace
 */
namespace FcFlightManagement;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcFlight\Model\FlightHeaderModel;
use FcFlightManagement\Model\RefuelIncomeInvoiceSearchModel;
use FcFlightManagement\Filter\RefuelIncomeInvoiceStep1Filter;
use FcFlightManagement\Model\RefuelIncomeInvoiceMainModel;
use FcFlightManagement\Model\RefuelIncomeInvoiceDataModel;
use FcFlightManagement\Model\RefuelOutcomeInvoiceSearchModel;
use FcFlightManagement\Filter\RefuelOutcomeInvoiceStep1Filter;
use FcFlightManagement\Model\RefuelOutcomeInvoiceMainModel;
use FcFlightManagement\Model\RefuelOutcomeInvoiceDataModel;
use FcFlightManagement\Model\ApServiceIncomeInvoiceSearchModel;
use FcFlightManagement\Filter\ApServiceIncomeInvoiceStep1Filter;
use FcFlightManagement\Model\ApServiceIncomeInvoiceMainModel;
use FcFlightManagement\Model\ApServiceIncomeInvoiceDataModel;
use FcFlightManagement\Model\ApServiceOutcomeInvoiceSearchModel;
use FcFlightManagement\Filter\ApServiceOutcomeInvoiceStep1Filter;
use FcFlightManagement\Model\ApServiceOutcomeInvoiceMainModel;
use FcFlightManagement\Model\ApServiceOutcomeInvoiceDataModel;
use FcFlightManagement\Model\PermissionIncomeInvoiceSearchModel;
use FcFlightManagement\Filter\PermissionIncomeInvoiceStep1Filter;
use FcFlightManagement\Model\PermissionIncomeInvoiceMainModel;
use FcFlightManagement\Model\PermissionIncomeInvoiceDataModel;
use FcFlightManagement\Model\PermissionOutcomeInvoiceSearchModel;
use FcFlightManagement\Filter\PermissionOutcomeInvoiceStep1Filter;
use FcFlightManagement\Model\PermissionOutcomeInvoiceMainModel;
use FcFlightManagement\Model\PermissionOutcomeInvoiceDataModel;

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
//    public function getViewHelperConfig()
//    {
//        return array(
//            'invokables' => array(
//                'numbersToWords' => 'FcFlightManagement\Helper\NumbersToWords',
//            ),
//        );
//    }

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
                'FcFlightManagement\Model\RefuelIncomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelIncomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\RefuelIncomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelIncomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\RefuelIncomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelIncomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\RefuelIncomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelIncomeInvoiceDataModel($dbAdapter);
                    },
                'FcFlightManagement\Model\RefuelOutcomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelOutcomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\RefuelOutcomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelOutcomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\RefuelOutcomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelOutcomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\RefuelOutcomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelOutcomeInvoiceDataModel($dbAdapter);
                    },

                'FcFlightManagement\Model\ApServiceIncomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceIncomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\ApServiceIncomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceIncomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\ApServiceIncomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceIncomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\ApServiceIncomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceIncomeInvoiceDataModel($dbAdapter);
                    },
                'FcFlightManagement\Model\ApServiceOutcomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceOutcomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\ApServiceOutcomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceOutcomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\ApServiceOutcomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceOutcomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\ApServiceOutcomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new ApServiceOutcomeInvoiceDataModel($dbAdapter);
                    },

                'FcFlightManagement\Model\PermissionIncomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionIncomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\PermissionIncomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionIncomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\PermissionIncomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionIncomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\PermissionIncomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionIncomeInvoiceDataModel($dbAdapter);
                    },
                'FcFlightManagement\Model\PermissionOutcomeInvoiceSearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionOutcomeInvoiceSearchModel($dbAdapter);
                    },
                'FcFlightManagement\Filter\PermissionOutcomeInvoiceStep1Filter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionOutcomeInvoiceStep1Filter($dbAdapter);
                    },
                'FcFlightManagement\Model\PermissionOutcomeInvoiceMainModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionOutcomeInvoiceMainModel($dbAdapter);
                    },
                'FcFlightManagement\Model\PermissionOutcomeInvoiceDataModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new PermissionOutcomeInvoiceDataModel($dbAdapter);
                    },
            ),
        );
    }
}

