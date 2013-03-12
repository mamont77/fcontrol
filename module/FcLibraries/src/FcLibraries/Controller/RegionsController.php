<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegionsController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
