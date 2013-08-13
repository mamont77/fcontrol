<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\RefuelForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;

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

        $form = new RefuelForm('refuel',
            array(
                'headerId' => $this->headerId,
                'libraries' => array(
                    'airports' => $this->getParentLeg(),
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
     * @return array
     */
    private function getParentLeg()
    {
        return $this->getLegModel()->getLegById($this->headerId);
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
