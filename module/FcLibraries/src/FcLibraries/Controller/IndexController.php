<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use FcLibrariesSearch\Form\AdvancedSearchForm;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package FcLibraries\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        return new ViewModel(array(
                'searchForm' => new AdvancedSearchForm(),
            )
        );
    }
}
