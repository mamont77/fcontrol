<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\FlightHeaderForm;
use FcFlight\Form\FlightDataForm;
use FcFlight\Form\RefuelForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class RefuelController extends AbstractActionController
{
    public $headerId;
    protected $refuelModel;
    protected $flightHeaderModel;
    protected $flightDataModel;
    protected $kontragentModel;
    protected $unitModel;

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {

        $this->headerId = (int)$this->params()->fromRoute('id', 0);
        if (!$this->headerId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'index'
            ));
        }

        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($this->headerId);

        $form = new RefuelForm('refuel',
            array(
                'headerId' => $this->headerId,
                'libraries' => array(
                    'airports' => $this->getParentData(),
                    'agents' => $this->getKontragents(),
                    'units' => $this->getUnits(),
                )
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\RefuelFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $summaryData = $this->getRefuelModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage('Refuel ' . $summaryData . ' was successfully added.');
                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $this->headerId,
            'refNumberOrder' => $refNumberOrder,
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
            $redirectPath = $this->getRefuelModel()->getHeaderRefNumberOrderByRefuelId($id);
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getRefuelModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Refuel was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toUrl('/browse/' . $redirectPath);
        }

        return array(
            'id' => $id,
            'data' => $this->getRefuelModel()->get($id)
        );
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

    /**
     * @return array|object
     */
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

    /**
     * @return array
     */
    private function getParentData()
    {
        return $this->getFlightDataModel()->getDataById($this->headerId);
    }

    /**
     * @return array|object
     */
    public function getLibraryUnitModel()
    {
        if (!$this->unitModel) {
            $sm = $this->getServiceLocator();
            $this->unitModel = $sm->get('FcLibraries\Model\UnitModel');
        }

        return $this->unitModel;
    }

    /**
     * @return mixed
     */
    private function getUnits()
    {
        return $this->getLibraryUnitModel()->fetchAll();
    }
}
