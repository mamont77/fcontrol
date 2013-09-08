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

    public function getValidatorConfig() {
        return array(
            'invokables' => array(
                'dateOfFlight' => 'FcFlight\Validator\FlightDateChecker'
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

