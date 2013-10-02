<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\RefuelForm;

class RefuelController extends FlightController
{

    protected $headerId;
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

        $refuels = $this->getRefuelModel()->getByHeaderId($this->headerId);
        $lastRefuel = end($refuels);
        if ($lastRefuel) {
            $previousDate = $lastRefuel['date'];
        } else {
            $previousDate = null;
        }

        $form = new RefuelForm('refuel',
            array(
                'headerId' => $this->headerId,
                'libraries' => array(
                    'airports' => $this->getParentLeg(),
                    'agents' => $this->getKontragents(),
                    'units' => $this->getUnits(),
                ),
                'previousValues' => array(
                    'previousDate' => $previousDate,
                ),
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
                $data = $this->getRefuelModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage('Refuel ' . $data['hash'] . ' was successfully added.');
                return $this->redirect()->toRoute('refuel',
                    array(
                        'action' => 'add',
                        'id' => $this->headerId,
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $this->headerId,
            'refNumberOrder' => $refNumberOrder,
            'refuels' => $refuels,

        );
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'index'
            ));
        }

        $refNumberOrder = $this->getRefuelModel()->getHeaderRefNumberOrderByRefuelId($id);

        $data = $this->getRefuelModel()->get($id);
        $this->headerId = (int)$data->headerId;

        $refuels = $this->getRefuelModel()->getByHeaderId($this->headerId);
        $lastRefuel = end($refuels);
        if ($lastRefuel) {
            $previousDate = $lastRefuel['date'];
        } else {
            $previousDate = null;
        }

        $form = new RefuelForm('refuel',
            array(
                'libraries' => array(
                    'airports' => $this->getParentLeg(),
                    'agents' => $this->getKontragents(),
                    'units' => $this->getUnits(),
                ),
                'previousValues' => array(
                    'previousDate' => $previousDate,
                ),
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\RefuelFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $summaryData = $this->getRefuelModel()->save($data);
                $this->flashMessenger()->addSuccessMessage('Refuel ' . $summaryData . ' was successfully saved.');

                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }

        return array('form' => $form,
            'id' => $data->id,
            'refNumberOrder' => $refNumberOrder,
            'refuels' => $refuels,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        $refUri = $request->getHeader('Referer')->uri()->getPath();
        $refNumberOrder = $this->getRefuelModel()->getHeaderRefNumberOrderByRefuelId($id);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getRefuelModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Refuel was successfully deleted.");
            }

            $redirectPath = (string)$request->getPost('referer');
            // Redirect to back
            return $this->redirect()->toUrl($redirectPath);
        }

        return array(
            'id' => $id,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getRefuelModel()->get($id)
        );
    }

    /**
     * @return array
     */
    private function getParentLeg()
    {
        return $this->getLegModel()->getByHeaderId($this->headerId);
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
