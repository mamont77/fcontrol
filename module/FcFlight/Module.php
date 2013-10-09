<?php
namespace FcFlight;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcFlight\Model\FlightHeaderModel;
use FcFlight\Model\SearchModel;
use FcFlight\Model\LegModel;
use FcFlight\Model\RefuelModel;
use FcFlight\Filter\FlightHeaderFilter;
use FcFlight\Filter\SearchFilter;
use FcFlight\Filter\LegFilter;
use FcFlight\Filter\RefuelFilter;

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

    public function getValidatorConfig()
    {
        return array(
            'invokables' => array(
                'FlightDateChecker' => 'FcFlight\Validator\FlightDateChecker',
                'FlightYearChecker' => 'FcFlight\Validator\FlightYearChecker',
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
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'CommonData' => function ($sm) {
                        $serviceLocator = $sm->getServiceLocator();
                        $aircraftModel = $serviceLocator->get('FcLibraries\Model\AircraftModel');
                        $aircraftTypeModel = $serviceLocator->get('FcLibraries\Model\AircraftTypeModel');
                        $airOperatorModel = $serviceLocator->get('FcLibraries\Model\AirOperatorModel');
                        $airportModel = $serviceLocator->get('FcLibraries\Model\AirportModel');
                        $baseOfPermitModel = $serviceLocator->get('FcLibraries\Model\BaseOfPermitModel');
                        $cityModel = $serviceLocator->get('FcLibraries\Model\CityModel');
                        $countryModel = $serviceLocator->get('FcLibraries\Model\CountryModel');
                        $currencyModel = $serviceLocator->get('FcLibraries\Model\CurrencyModel');
                        $kontragentModel = $serviceLocator->get('FcLibraries\Model\KontragentModel');
                        $regionModel = $serviceLocator->get('FcLibraries\Model\RegionModel');
                        $unitModel = $serviceLocator->get('FcLibraries\Model\UnitModel');
                        $flightHeaderModel = $serviceLocator->get('FcFlight\Model\FlightHeaderModel');
                        $legModel = $serviceLocator->get('FcFlight\Model\LegModel');
                        $refuelModel = $serviceLocator->get('FcFlight\Model\RefuelModel');
                        $searchModel = $serviceLocator->get('FcFlight\Model\SearchModel');
                        $controllerPlugin = new Controller\Plugin\CommonData;
                        $controllerPlugin->setAircraftModel($aircraftModel)
                            ->setAircraftModel($aircraftModel)
                            ->setAircraftTypeModel($aircraftTypeModel)
                            ->setAirOperatorModel($airOperatorModel)
                            ->setAirportModel($airportModel)
                            ->setBaseOfPermitModel($baseOfPermitModel)
                            ->setCityModel($cityModel)
                            ->setCountryModel($countryModel)
                            ->setCurrencyModel($currencyModel)
                            ->setKontragentModel($kontragentModel)
                            ->setRegionModel($regionModel)
                            ->setUnitModel($unitModel)
                            ->setFlightHeaderModel($flightHeaderModel)
                            ->setLegModel($legModel)
                            ->setRefuelModel($refuelModel)
                            ->setSearchModel($searchModel);
                        return $controllerPlugin;
                    },
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
                'FcFlight\Model\SearchModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new SearchModel($dbAdapter);
                    },
                'FcFlight\Model\LegModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new LegModel($dbAdapter);
                    },
                'FcFlight\Model\RefuelModel' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelModel($dbAdapter);
                    },
                'FcFlight\Filter\FlightHeaderFilter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new FlightHeaderFilter($dbAdapter);
                    },
                'FcFlight\Filter\SearchFilter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new SearchFilter($dbAdapter);
                    },
                'FcFlight\Filter\LegFilter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new LegFilter($dbAdapter);
                    },
                'FcFlight\Filter\RefuelFilter' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new RefuelFilter($dbAdapter);
                    },
            ),
        );
    }
}

