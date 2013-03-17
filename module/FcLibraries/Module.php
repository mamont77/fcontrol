<?php
namespace FcLibraries;

use FcLibraries\Model\Region;
//use FcLibraries\Model\Country;
//use FcLibraries\Model\Airport;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'FcLibraries\Model\Region' =>  function($sm) {
                    $tableGateway = $sm->get('RegionTableGateway');
                    $table = new Region($tableGateway);
                    return $table;
                },
//                'RegionTableGateway' => function ($sm) {
//                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $resultSetPrototype = new ResultSet();
//                    $resultSetPrototype->setArrayObjectPrototype(new Region());
//                    return new TableGateway('region', $dbAdapter, null, $resultSetPrototype);
//                },
            ),
        );
    }
}
