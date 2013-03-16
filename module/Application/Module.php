<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

//use Zend\ModuleManager\ModuleManager;
//use Zend\Mvc\ModuleRouteListener;
//use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        $moduleConfig = include __DIR__ . '/config/module.config.php';
        $navConfig = include __DIR__ . '/config/nav.config.php';
        $config = array_merge($moduleConfig, $navConfig);
        return $config;
    }

    public function init(\Zend\ModuleManager\ModuleManager $moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', array($this, 'onModuleDispatch'));
    }

    public function onModuleDispatch(\Zend\Mvc\MvcEvent $e)
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

//    public function onBootstrap(MvcEvent $e)
//    {
//        $e->getApplication()->getServiceManager()->get('translator');
//        $eventManager        = $e->getApplication()->getEventManager();
//        $moduleRouteListener = new ModuleRouteListener();
//        $moduleRouteListener->attach($eventManager);
//    }


}
