<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Country;
use FcLibraries\Form\CountryForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class CountryController extends AbstractActionController implements ControllerInterface
{
    protected $_countryTable;
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

        $albums = $this->getCountryTable()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/countries',
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CountryForm('country', array('regions' => $this->getRegions()));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = $this->getServiceLocator()->get('CountryModel');
            $form->setInputFilter($model->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $model->exchangeArray($form->getData());
                $this->getCountryTable()->add($model);
                return $this->redirect()->toRoute('zfcadmin/country', array(
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
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/country', array(
                'action' => 'add'
            ));
        }
        $data = $this->getCountryTable()->get($id);

        $form = new CountryForm('country', array('regions' => $this->getRegions()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($data->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getCountryTable()->save($form->getData());

                return $this->redirect()->toRoute('zfcadmin/countries');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getCountryTable()->remove($id);
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        return array(
            'id' => $id,
            'data' => $this->getCountryTable()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getCountryTable()
    {
        if (!$this->_countryTable) {
            $sm = $this->getServiceLocator();
            $this->_countryTable = $sm->get('FcLibraries\Model\CountryTable');
        }
        return $this->_countryTable;
    }

    /**
     * @return array|object
     */
    private function getRegionTable()
    {
        if (!$this->_regionTable) {
            $sm = $this->getServiceLocator();
            $this->_regionTable = $sm->get('FcLibraries\Model\RegionTable');
        }
        return $this->_regionTable;
    }

    /**
     * @return mixed
     */
    private function getRegions() {
        return $this->getRegionTable()->fetchAll();
    }
}
