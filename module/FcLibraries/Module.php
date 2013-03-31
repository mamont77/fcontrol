<?php
namespace FcLibraries;

use FcLibraries\Model\RegionTable;
use FcLibraries\Form\RegionFilter;
use FcLibraries\Model\CountryTable;
use FcLibraries\Model\AirportTable;

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
            'invokables' => array(
                'RegionModel' => 'FcLibraries\Model\Region',
            ),
            'factories' => array(
                'FcLibraries\Model\RegionTable' => function ($sm) {
                    $region = $sm->get('RegionModel');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionTable($dbAdapter, $region);
                },
                'FcLibraries\Model\CountryTable' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CountryTable($dbAdapter);
                },
                'FcLibraries\Model\AirportTable' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AirportTable($dbAdapter);
                },
                'FcLibraries\Form\RegionFilter' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new RegionFilter($dbAdapter);
                },
            ),
        );
    }
}
