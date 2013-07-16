<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Application\Service\ErrorHandling as ErrorHandlingService;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;

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

    public function onBootstrap($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('dispatch.error', function ($event) {
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('Application\Service\ErrorHandling');
                $service->logException($exception);
            }
        });
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Service\ErrorHandling' =>  function($sm) {
                    $logger = $sm->get('Zend\Log');
                    $service = new ErrorHandlingService($logger);
                    return $service;
                },
                'Zend\Log' => function ($sm) {
                    $filename = 'log_' . date('F') . '.txt';
                    $log = new Logger();
                    $writer = new LogWriterStream('./data/logs/' . $filename);
                    $log->addWriter($writer);

                    return $log;
                },
            ),
        );
    }
}
