<?php
/**
 * @namespace
 */
namespace FcLibrariesSearch;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use FcLibrariesSearch\Model\SearchModel;
use FcLibrariesSearch\Filter\SearchFilter;
use FcLibrariesSearch\Filter\AdvancedSearchFilter;

/**
 * Class Module
 * @package FcLibrariesSearch
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
                'FcLibrariesSearch\Model\SearchModel' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new SearchModel($dbAdapter);
                },
                'FcLibrariesSearch\Filter\SearchFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new SearchFilter($dbAdapter);
                },
                'FcLibrariesSearch\Filter\AdvancedSearchFilter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new AdvancedSearchFilter($dbAdapter);
                },
            ),
        );
    }
}
