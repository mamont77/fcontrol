<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\FlightForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class FlightController extends AbstractActionController
{
    protected $aircraftModel;
    protected $aircraftTypeModel;

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

        $data = $this->getAircraftModel()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 20;

        $data->current();
        $pagination = new Paginator(new paginatorIterator($data));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'zfcadmin/aircrafts',
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new AircraftForm('aircraft', array('aircraft_types' => $this->getAircraftTypes()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AircraftFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $this->getAircraftModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Aircraft '"
                        . $data['reg_number'] . "' was successfully added.");
                return $this->redirect()->toRoute('zfcadmin/aircraft', array(
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
            return $this->redirect()->toRoute('zfcadmin/aircraft', array(
                'action' => 'add'
            ));
        }
        $data = $this->getAircraftModel()->get($id);

        $form = new AircraftForm('aircraft', array('aircraft_types' => $this->getAircraftTypes()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AircraftFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getAircraftModel()->save($data);
                $this->flashMessenger()->addSuccessMessage("Aircraft '"
                        . $data->name . "' was successfully saved.");
                return $this->redirect()->toRoute('zfcadmin/aircrafts');
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
            return $this->redirect()->toRoute('zfcadmin/aircrafts');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $reg_number = (string) $request->getPost('reg_number');
                $this->getAircraftModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Aircraft '"
                        . $reg_number . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        return array(
            'id' => $id,
            'data' => $this->getAircraftModel()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getAircraftModel()
    {
        if (!$this->aircraftModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftModel = $sm->get('FcLibraries\Model\AircraftModel');
        }
        return $this->aircraftModel;
    }

    /**
     * @return array|object
     */
    private function getAircraftTypeModel()
    {
        if (!$this->aircraftTypeModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftTypeModel = $sm->get('FcLibraries\Model\AircraftTypeModel');
        }
        return $this->aircraftTypeModel;
    }

    /**
     * @return mixed
     */
    private function getAircraftTypes()
    {
        return $this->getAircraftTypeModel()->fetchAll();
    }
}
