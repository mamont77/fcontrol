<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Region;
use FcLibraries\Form\RegionForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class RegionController extends AbstractActionController implements ControllerInterface
{
    protected $_regionTable;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'name';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $albums = $this->getRegionTable()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 5;

        $albums->current();
        $pagination = new Paginator(new paginatorIterator($albums));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'zfcadmin/regions',
        ));
    }

    public function addAction()
    {
        $form = new RegionForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
//            $model = new Region();
            $model = $this->getServiceLocator()->get('RegionModel');
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

    /**
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/region', array(
                'action' => 'add'
            ));
        }
        $data = $this->getRegionTable()->get($id);
        //$data = $this->getServiceLocator()->get('RegionModel');

        $form  = new RegionForm();
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($data->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRegionTable()->save($form->getData());

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

            // Redirect to list
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
