<?php
namespace FcLibraries;

use FcLibraries\Model\RegionModel;
use FcLibraries\Filter\RegionFilter;
use FcLibraries\Model\CountryModel;
use FcLibraries\Filter\CountryFilter;
use FcLibraries\Model\AirportModel;
use FcLibraries\Filter\AirportFilter;

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
            ),
        );
    }
}
