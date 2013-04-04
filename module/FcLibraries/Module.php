<?php
namespace FcLibraries;

use FcLibraries\Model\RegionModel;
use FcLibraries\Filter\RegionFilter;
use FcLibraries\Model\CountryModel;
use FcLibraries\Filter\CountryFilter;
use FcLibraries\Model\AirportModel;
use FcLibraries\Filter\AirportFilter;
use FcLibraries\Model\AircraftTypeModel;
use FcLibraries\Filter\AircraftTypeFilter;
use FcLibraries\Model\AircraftModel;
use FcLibraries\Filter\AircraftFilter;
use FcLibraries\Model\AirOperatorModel;
use FcLibraries\Filter\AirOperatorFilter;
use FcLibraries\Model\KontragentModel;
use FcLibraries\Filter\KontragentFilter;

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
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(),
            'factories' => array(
                'FcLibraries\Model\RegionModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionModel($dbAdapter);
                },
                'FcLibraries\Filter\RegionFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionFilter($dbAdapter);
                },
                'FcLibraries\Model\CountryModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CountryModel($dbAdapter);
                },
                'FcLibraries\Filter\CountryFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CountryFilter($dbAdapter);
                },
                'FcLibraries\Model\AirportModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirportModel($dbAdapter);
                },
                'FcLibraries\Filter\AirportFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirportFilter($dbAdapter);
                },
                'FcLibraries\Model\AircraftTypeModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftTypeModel($dbAdapter);
                },
                'FcLibraries\Filter\AircraftTypeFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftTypeFilter($dbAdapter);
                },
                'FcLibraries\Model\AircraftModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftModel($dbAdapter);
                },
                'FcLibraries\Filter\AircraftFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AircraftFilter($dbAdapter);
                },
                'FcLibraries\Model\AirOperatorModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirOperatorModel($dbAdapter);
                },
                'FcLibraries\Filter\AirOperatorFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirOperatorFilter($dbAdapter);
                },
                'FcLibraries\Model\KontragentModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new KontragentModel($dbAdapter);
                },
                'FcLibraries\Filter\KontragentFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new KontragentFilter($dbAdapter);
                },
            ),
        );
    }
}
