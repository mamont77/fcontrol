<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Region;
use FcLibraries\Form\RegionForm;
use FcLibraries\Form\RegionFormInputFilter;


class RegionController extends AbstractActionController implements ControllerInterface
{
    protected $_regionTable;


    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $form = new RegionForm();
        $formType = \DluTwBootstrap\Form\FormUtil::FORM_TYPE_HORIZONTAL;
        $inputFilter = new RegionFormInputFilter();
        $form->setInputFilter($inputFilter);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($inputFilter);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $model = new Region();
                $model->exchangeArray($data);
                $model->add($model);

                // Redirect to list of users
                return $this->redirect()->toRoute('zfcadmin/region/add');
            }
        }
        return array('form' => $form);



    }

    public function editAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

    public function getModelTable()
    {
        if (!$this->_regionTable) {
            $sm = $this->getServiceLocator();
            $this->_regionTable = $sm->get('FcLibraries\Model\Region');
        }
        return $this->_regionTable;
    }
}
