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
    protected $flightModel;
    protected $kontragentModel;
    protected $airOperatorModel;
    protected $aircraftModel;

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
        $data = $this->getFlightModel()->fetchAll($select->order($order_by . ' ' . $order)); //TODO fix order by refNumberOrder
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
    public function addAction()
    {

        $form = new FlightForm('flight',
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
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $refNumberOrder = $this->getFlightModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                    . $refNumberOrder . "' was successfully added.");
                return $this->redirect()->toRoute('flight', array(
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
            return $this->redirect()->toRoute('flight', array(
                'action' => 'add'
            ));
        }
        $data = $this->getFlightModel()->get($id);

        $form = new FlightForm('flight',
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
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getFlightModel()->save($data);
                $this->flashMessenger()->addSuccessMessage("Flights '"
                    . $data->refNumberOrder . "' was successfully saved.");
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
    public function deleteAction()
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
                $this->getFlightModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Aircraft '"
                    . $refNumberOrder . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('flights');
        }

        return array(
            'id' => $id,
            'data' => $this->getFlightModel()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getFlightModel()
    {
        if (!$this->flightModel) {
            $sm = $this->getServiceLocator();
            $this->flightModel = $sm->get('FcFlight\Model\FlightModel');
        }
        return $this->flightModel;
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
}
