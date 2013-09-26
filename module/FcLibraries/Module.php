<?php
namespace FcLibraries;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcLibraries\Model\AircraftModel;
use FcLibraries\Filter\AircraftFilter;
use FcLibraries\Model\AircraftTypeModel;
use FcLibraries\Filter\AircraftTypeFilter;
use FcLibraries\Model\AirOperatorModel;
use FcLibraries\Filter\AirOperatorFilter;
use FcLibraries\Model\AirportModel;
use FcLibraries\Filter\AirportFilter;
use FcLibraries\Model\BaseOfPermitModel;
use FcLibraries\Filter\BaseOfPermitFilter;
use FcLibraries\Model\CityModel;
use FcLibraries\Filter\CityFilter;
use FcLibraries\Model\CountryModel;
use FcLibraries\Filter\CountryFilter;
use FcLibraries\Model\CurrencyModel;
use FcLibraries\Filter\CurrencyFilter;
use FcLibraries\Model\KontragentModel;
use FcLibraries\Filter\KontragentFilter;
use FcLibraries\Model\RegionModel;
use FcLibraries\Filter\RegionFilter;
use FcLibraries\Model\UnitModel;
use FcLibraries\Filter\UnitFilter;

/**
 * Class Module
 * @package FcLibraries
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
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(),
            'factories' => array(
                'FcLibraries\Model\AircraftModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftModel($dbAdapter);
                },
                'FcLibraries\Filter\AircraftFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftFilter($dbAdapter);
                },
                'FcLibraries\Model\AircraftTypeModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftTypeModel($dbAdapter);
                },
                'FcLibraries\Filter\AircraftTypeFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftTypeFilter($dbAdapter);
                },
                'FcLibraries\Model\AirOperatorModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirOperatorModel($dbAdapter);
                },
                'FcLibraries\Filter\AirOperatorFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirOperatorFilter($dbAdapter);
                },
                'FcLibraries\Model\AirportModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirportModel($dbAdapter);
                },
                'FcLibraries\Filter\AirportFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirportFilter($dbAdapter);
                },
                'FcLibraries\Model\BaseOfPermitModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new BaseOfPermitModel($dbAdapter);
                },
                'FcLibraries\Filter\BaseOfPermitFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new BaseOfPermitFilter($dbAdapter);
                },
                'FcLibraries\Model\CityModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CityModel($dbAdapter);
                },
                'FcLibraries\Filter\CityFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CityFilter($dbAdapter);
                },
                'FcLibraries\Model\CountryModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CountryModel($dbAdapter);
                },
                'FcLibraries\Filter\CountryFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CountryFilter($dbAdapter);
                },
                'FcLibraries\Model\CurrencyModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CurrencyModel($dbAdapter);
                },
                'FcLibraries\Filter\CurrencyFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CurrencyFilter($dbAdapter);
                },
                'FcLibraries\Model\KontragentModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new KontragentModel($dbAdapter);
                },
                'FcLibraries\Filter\KontragentFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new KontragentFilter($dbAdapter);
                },
                'FcLibraries\Model\RegionModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionModel($dbAdapter);
                },
                'FcLibraries\Filter\RegionFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionFilter($dbAdapter);
                },
                'FcLibraries\Model\UnitModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new UnitModel($dbAdapter);
                },
                'FcLibraries\Filter\UnitFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new UnitFilter($dbAdapter);
                },
            ),
        );
    }
}
