<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Region;
use FcLibraries\Form\RegionForm;

class RegionController extends AbstractActionController implements ControllerInterface
{
    protected $_regionTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'data' => $this->getRegionTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new RegionForm();
//        $formType = \DluTwBootstrap\Form\FormUtil::FORM_TYPE_HORIZONTAL;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = new Region();
            $form->setInputFilter($model->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $model->exchangeArray($form->getData());
                $this->getRegionTable()->add($model);
                return $this->redirect()->toRoute('zfcadmin/region', array(
                    'action' => 'add'
                ));
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/region', array(
                'action' => 'add'
            ));
        }
        $data = $this->getRegionTable()->get($id);

        $form  = new RegionForm();
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($data->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRegionTable()->update($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('zfcadmin/regions');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/regions');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRegionTable()->remove($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('zfcadmin/regions');
        }

        return array(
            'id'    => $id,
            'data' => $this->getRegionTable()->get($id)
        );
    }

    public function getRegionTable()
    {
        if (!$this->_regionTable) {
            $sm = $this->getServiceLocator();
            $this->_regionTable = $sm->get('FcLibraries\Model\RegionTable');
        }
        return $this->_regionTable;
    }
}
