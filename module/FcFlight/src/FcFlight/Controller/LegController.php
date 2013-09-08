<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\LegForm;

class LegController extends FlightController
{

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {

        $headerId = (int)$this->params()->fromRoute('id', 0);
        if (!$headerId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'index'
            ));
        }

        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($headerId);

        $legs = $this->getLegModel()->getByHeaderId($headerId);
        $lastLeg = end($legs);

        if ($lastLeg) {
            $previousDate = $lastLeg['dateOfFlight'];
            $preSelectedApDep = $lastLeg['apArrIcaoAndIata'];
        } else {
            $previousDate = null;
            $preSelectedApDep = null;
        }

        $form = new LegForm('leg',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'flightNumberIcaoAndIata' => $this->getAirOperators(),
                    'appIcaoAndIata' => $this->getAirports(),
                ),
                'previousValues' => array(
                    'previousDate' => $previousDate,
                    'preSelected' => array(
                        'apDepIcaoAndIata' => $preSelectedApDep,
                    ),
                ),
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\LegFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $summaryData = $this->getLegModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage('Leg ' . $summaryData . ' was successfully added.');
                return $this->redirect()->toRoute('leg',
                    array(
                        'action' => 'add',
                        'id' => $headerId,
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $headerId,
            'refNumberOrder' => $refNumberOrder,
            'legs' => $legs,
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

        $refNumberOrder = $this->getLegModel()->getHeaderRefNumberOrderByLegId($id);

        $data = $this->getLegModel()->get($id);

        $legs = $this->getLegModel()->getByHeaderId($data->headerId);
        $lastLeg = end($legs);

        if ($lastLeg) {
            $previousDate = $lastLeg['dateOfFlight'];
            $preSelectedApDep = $lastLeg['apArrIcaoAndIata'];
        } else {
            $previousDate = null;
            $preSelectedApDep = null;
        }

        $data->flightNumber['flightNumberIcaoAndIata'] = $data->flightNumberIcaoAndIata;
        $data->flightNumber['flightNumberText'] = $data->flightNumberText;
        $data->apDep['apDepIcaoAndIata'] = $data->apDepIcaoAndIata;
        $data->apDep['apDepTime'] = $data->apDepTime;
        $data->apArr['apArrIcaoAndIata'] = $data->apArrIcaoAndIata;
        $data->apArr['apArrTime'] = $data->apArrTime;

        $form = new LegForm('leg',
            array(
                'libraries' => array(
                    'flightNumberIcaoAndIata' => $this->getAirOperators(),
                    'appIcaoAndIata' => $this->getAirports(),
                ),
                'previousValues' => array(
                    'previousDate' => $previousDate,
                    'preSelected' => array(
                        'apDepIcaoAndIata' => $preSelectedApDep,
                    ),
                ),
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\LegFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $summaryData = $this->getLegModel()->save($data);
                $this->flashMessenger()->addSuccessMessage('Leg ' . $summaryData . ' was successfully saved.');

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
            'legs' => $legs,
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
        $refNumberOrder = $this->getLegModel()->getHeaderRefNumberOrderByLegId($id);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getLegModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Leg was successfully deleted.");
            }

            $redirectPath = (string)$request->getPost('referer');
            // Redirect to back
            return $this->redirect()->toUrl($redirectPath);
        }

        return array(
            'id' => $id,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'leg' => $this->getLegModel()->get($id)
        );
    }
}
