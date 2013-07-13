<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\FlightHeaderForm;
use FcFlight\Form\FlightDataForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class FlightController extends AbstractActionController
{
    protected $flightHeaderModel;
    protected $flightDataModel;
    protected $kontragentModel;
    protected $airOperatorModel;
    protected $aircraftModel;
    protected $airportModel;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'refNumberOrder';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $data = $this->getFlightHeaderModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'flights',
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function showAction()
    {
        $refNumberOrder = (string)$this->params()->fromRoute('refNumberOrder', '');
        $refNumberOrder = urldecode($refNumberOrder);

        if (empty($refNumberOrder)) {
            return $this->redirect()->toRoute('flights', array(
                'action' => 'index'
            ));
        }

        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $header->current();

        foreach ($header as $item) {
            $header = $item;
            break;
        }

        $data = $this->getFlightDataModel()->getDataById($header->id);

        return new ViewModel(array(
            'header' => $header,
            'data' => $data,
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addHeaderAction()
    {

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->getKontragents(),
                    'air_operator' => $this->getAirOperators(),
                    'aircraft' => $this->getAircrafts(),
                )
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightHeaderFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $refNumberOrder = $this->getFlightHeaderModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                    . $refNumberOrder . "' was successfully added.");
                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }
        return array('form' => $form);
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function editHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'add'
            ));
        }
        $data = $this->getFlightHeaderModel()->get($id);

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->getKontragents(),
                    'air_operator' => $this->getAirOperators(),
                    'aircraft' => $this->getAircrafts(),
                )
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightHeaderFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $refNumberOrder = $this->getFlightHeaderModel()->save($data);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                    . $refNumberOrder . "' was successfully saved.");
                return $this->redirect()->toRoute('flights');
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
    public function deleteHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flights');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $refNumberOrder = (string)$request->getPost('refNumberOrder');
                $this->getFlightHeaderModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Aircraft '"
                    . $refNumberOrder . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('flights');
        }

        return array(
            'id' => $id,
            'data' => $this->getFlightHeaderModel()->get($id)
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addDataAction()
    {

        $parentFormId = (int)$this->params()->fromRoute('id', 0);
        if (!$parentFormId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'index'
            ));
        }

        $form = new FlightDataForm('flightData',
            array(
                'parentFormId' => $parentFormId,
                'libraries' => array(
                    'flightNumberIcaoAndIata' => $this->getAirOperators(),
                    'appIcaoAndIata' => $this->getAirports(),
                )
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightDataFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($data['parentFormId']);
                $summaryData = $this->getFlightDataModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Form 1 '"
                . $summaryData . "' was successfully added.");
                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }
        return array('form' => $form, 'parentFormId' => $parentFormId);
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function editDataAction()
    {
        //todo

    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteDataAction()
    {
        //todo

    }

    /**
     * @return array|object
     */
    public function getFlightHeaderModel()
    {
        if (!$this->flightHeaderModel) {
            $sm = $this->getServiceLocator();
            $this->flightHeaderModel = $sm->get('FcFlight\Model\FlightHeaderModel');
        }
        return $this->flightHeaderModel;
    }

    /**
     * @return array|object
     */
    public function getFlightDataModel()
    {
        if (!$this->flightDataModel) {
            $sm = $this->getServiceLocator();
            $this->flightDataModel = $sm->get('FcFlight\Model\FlightDataModel');
        }
        return $this->flightDataModel;
    }

    public function getLibraryKontragentModel()
    {
        if (!$this->kontragentModel) {
            $sm = $this->getServiceLocator();
            $this->kontragentModel = $sm->get('FcLibraries\Model\KontragentModel');
        }

        return $this->kontragentModel;
    }

    /**
     * @return mixed
     */
    private function getKontragents()
    {
        return $this->getLibraryKontragentModel()->fetchAll();
    }


    public function getLibraryAirOperatorModel()
    {
        if (!$this->airOperatorModel) {
            $sm = $this->getServiceLocator();
            $this->airOperatorModel = $sm->get('FcLibraries\Model\AirOperatorModel');
        }

        return $this->airOperatorModel;
    }

    /**
     * @return mixed
     */
    private function getAirOperators()
    {
        return $this->getLibraryAirOperatorModel()->fetchAll();
    }

    public function getLibraryAircraftModel()
    {
        if (!$this->aircraftModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftModel = $sm->get('FcLibraries\Model\AircraftModel');
        }

        return $this->aircraftModel;
    }

    /**
     * @return mixed
     */
    private function getAircrafts()
    {
        return $this->getLibraryAircraftModel()->fetchAll();
    }

    public function getLibraryAirportModel()
    {
        if (!$this->airportModel) {
            $sm = $this->getServiceLocator();
            $this->airportModel = $sm->get('FcLibraries\Model\AirportModel');
        }

        return $this->airportModel;
    }

    /**
     * @return mixed
     */
    private function getAirports()
    {
        return $this->getLibraryAirportModel()->fetchAll();
    }
}
