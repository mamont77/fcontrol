<?php
/**
 * CommonData Helper for FcLibraries module.
 * @author Ruslan Piskarev
 */

namespace FcLibraries\Controller\Plugin;

use Zend\Di\ServiceLocator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class CommonData
 * @package FcLibraries\Controller\Plugin
 */
class CommonData extends AbstractPlugin
{
    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}