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
    /**
     * @var array
     */
    protected $dataForLogger = array();

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
            $data = $this->CommonData()->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
            . ', id ' . $orderAs));
        } else {
            $data = $this->CommonData()->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs));
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
            $data = $this->CommonData()->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
            . ', id ' . $orderAs), 0);
        } else {
            $data = $this->CommonData()->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs), 0);
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

        $header = $this->CommonData()->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);

        $legs = $this->CommonData()->getLegModel()->getByHeaderId($header->id);
        $refuels = $this->CommonData()->getRefuelModel()->getByHeaderId($header->id);

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
                    'kontragent' => $this->CommonData()->getKontragents(),
                    'air_operator' => $this->CommonData()->getAirOperators(),
                    'aircraft' => $this->CommonData()->getAircrafts(),
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
                $data = $this->CommonData()->getFlightHeaderModel()->add($filter);

                $message = "Flights '" . $data['refNumberOrder'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getFlightHeaderModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Info($loggerPlugin->getLogMessage());

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
        $data = $this->CommonData()->getFlightHeaderModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->CommonData()->getKontragents(),
                    'air_operator' => $this->CommonData()->getAirOperators(),
                    'aircraft' => $this->CommonData()->getAircrafts(),
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
                $refNumberOrder = $this->CommonData()->getFlightHeaderModel()->save($data);

                $message = "Flights '" . $refNumberOrder . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->CommonData()->getFlightHeaderModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Notice($loggerPlugin->getLogMessage());

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

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getFlightHeaderModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $refNumberOrder = (string)$request->getPost('refNumberOrder');
                $this->CommonData()->getFlightHeaderModel()->remove($id);

                $message = "Flights '" . $refNumberOrder . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('home');
        }

        return array(
            'id' => $id,
            'data' => $this->CommonData()->getFlightHeaderModel()->get($id)
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
        $data = $this->CommonData()->getFlightHeaderModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $data->status = ($data->status) ? 0 : 1;

        $this->CommonData()->getFlightHeaderModel()->save($data);

        $message = "Status for flights '" . $data->refNumberOrder . "' was successfully switched.";
        $this->flashMessenger()->addSuccessMessage($message);

        $this->setDataForLogger($data);
        $loggerPlugin->setNewLogRecord($this->dataForLogger);
        $loggerPlugin->setLogMessage($message);

        $logger = $this->getServiceLocator()->get('logger');
        $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
        $logger->Notice($loggerPlugin->getLogMessage());

        return $this->redirect()->toRoute('browse',
            array(
                'action' => 'show',
                'refNumberOrder' => $data->refNumberOrder,
            ));
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Date Order' => $data->dateOrder,
            'Ref Number' => $data->refNumberOrder,
            'Customer' => $data->kontragentShortName,
            'Air Operator' => $data->airOperatorShortName,
            'Aircraft' => $data->aircraft . ' (' . $data->aircraftTypeName . ')',
            'Status' => ($data->status == 1) ? 'In process' : 'Done'
        );
    }
}
