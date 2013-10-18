<?php
/**
 * @namespace
 */
namespace FcAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package FcAdmin\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function dashboardAction()
    {
        return new ViewModel();
    }
}
