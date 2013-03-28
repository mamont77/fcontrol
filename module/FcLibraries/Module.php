<?php
namespace FcLibraries;

use FcLibraries\Model\RegionTable;
use FcLibraries\Model\CountryTable;

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
            'factories' => array(
                'FcLibraries\Model\RegionTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RegionTable($dbAdapter);
                    return $table;
                },
                'FcLibraries\Model\CountryTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CountryTable($dbAdapter);
                    return $table;
                },
            ),
            'invokables' => array(
                'RegionModel' => 'FcLibraries\Model\Region',
            ),

        );
    }
}
