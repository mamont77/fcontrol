<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\FlightHeaderForm;
use FcFlight\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class FlightController extends AbstractActionController
{
    protected $flightHeaderModel;
    protected $legModel;
    protected $refuelModel;
    protected $kontragentModel;
    protected $airOperatorModel;
    protected $aircraftModel;
    protected $airportModel;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
//    public function indexAction()
//    {
//        $select = new Select();
//
//        $orderByMaster = 'dateOrder';
//        $orderAsType = Select::ORDER_DESCENDING;
//
//        $orderBy = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : $orderByMaster;
//        $orderAs = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : $orderAsType;
//
//        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
//        if ($orderBy == $orderByMaster && $orderAsType == $orderAs) {
//            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
//            . ', id ' . $orderAs));
//        } else {
//            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs));
//        }
//        $itemsPerPage = 20;
//        $data->current();
//
//        $pagination = new Paginator(new paginatorIterator($data));
//        $pagination->setCurrentPageNumber($page)
//            ->setItemCountPerPage($itemsPerPage)
//            ->setPageRange(7);
//
//        return new ViewModel(array(
//            'order_by' => $orderBy,
//            'order' => $orderAs,
//            'page' => $page,
//            'pagination' => $pagination,
//            'route' => 'flightsActive',
//        ));
//    }

    /**
     * @return ViewModel
     */
    public function activeAction()
    {
        $select = new Select();

        $orderByMaster = 'dateOrder';
        $orderAsType = Select::ORDER_DESCENDING;

        $orderBy = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : $orderByMaster;
        $orderAs = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : $orderAsType;

        if ($orderBy == $orderByMaster && $orderAsType == $orderAs) {
            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
            . ', id ' . $orderAs));
        } else {
            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs));
        }
        $data->current();

        return new ViewModel(array(
            'order_by' => $orderBy,
            'order' => $orderAs,
            'data' => $data,
            'searchForm' => new SearchForm(),
            'route' => 'flightsActive',
        ));
    }

    /**
     * @return ViewModel
     */
    public function archivedAction()
    {
        $select = new Select();

        $orderByMaster = 'dateOrder';
        $orderAsType = Select::ORDER_DESCENDING;

        $orderBy = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : $orderByMaster;
        $orderAs = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : $orderAsType;

        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        if ($orderBy == $orderByMaster && $orderAsType == $orderAs) {
            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
            . ', id ' . $orderAs), 0);
        } else {
            $data = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs), 0);
        }
        $itemsPerPage = 20;
        $data->current();

        $pagination = new Paginator(new paginatorIterator($data));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $orderBy,
            'order' => $orderAs,
            'page' => $page,
            'pagination' => $pagination,
            'searchForm' => new SearchForm(),
            'route' => 'flightsArchived',
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
            return $this->redirect()->toRoute('home', array(
                'action' => 'active'
            ));
        }

        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);

        $legs = $this->getLegModel()->getByHeaderId($header->id);
        $refuels = $this->getRefuelModel()->getByHeaderId($header->id);

        return new ViewModel(array(
            'header' => $header,
            'legs' => $legs,
            'refuels' => $refuels,
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
                $data = $this->getFlightHeaderModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                . $data['refNumberOrder'] . "' was successfully added.");
                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $data['refNumberOrder'],
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
                return $this->redirect()->toRoute('home');
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
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $refNumberOrder = (string)$request->getPost('refNumberOrder');
                $this->getFlightHeaderModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                . $refNumberOrder . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('home');
        }

        return array(
            'id' => $id,
            'data' => $this->getFlightHeaderModel()->get($id)
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function statusHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        $data = $this->getFlightHeaderModel()->get($id);

        $data->status = ($data->status) ? 0 : 1;

        $this->getFlightHeaderModel()->save($data);
        $this->flashMessenger()->addSuccessMessage("Status for flights '"
        . $data->refNumberOrder . "' was successfully switched.");
        return $this->redirect()->toRoute('browse',
            array(
                'action' => 'show',
                'refNumberOrder' => $data->refNumberOrder,
            ));
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
    public function getLegModel()
    {
        if (!$this->legModel) {
            $sm = $this->getServiceLocator();
            $this->legModel = $sm->get('FcFlight\Model\LegModel');
        }
        return $this->legModel;
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
    public function getKontragents()
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
    public function getAirOperators()
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
    public function getAircrafts()
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
    public function getAirports()
    {
        return $this->getLibraryAirportModel()->fetchAll();
    }

    /**
     * @return array|object
     */
    public function getRefuelModel()
    {
        if (!$this->refuelModel) {
            $sm = $this->getServiceLocator();
            $this->refuelModel = $sm->get('FcFlight\Model\RefuelModel');
        }
        return $this->refuelModel;
    }
}
