<?php

namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\LegForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;

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

        $form = new LegForm('leg',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'flightNumberIcaoAndIata' => $this->getAirOperators(),
                    'appIcaoAndIata' => $this->getAirports(),
                )
            )
        );

        $leg = $this->getLegModel()->getLegById($headerId);


        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\LegFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $summaryData = $this->getLegModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage('Leg '. $summaryData . ' was successfully added.');
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
            'leg' => $leg,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     * @deprecated
     */
//    public function editAction()
//    {
//        $id = (int)$this->params()->fromRoute('id', 0);
//        $data = $this->getLegModel()->get($id);
//
//        $form = new LegForm('leg',
//            array(
//                //'headerId' => $id,
//                'libraries' => array(
//                    'flightNumberIcaoAndIata' => $this->getAirOperators(),
//                    'appIcaoAndIata' => $this->getAirports(),
//                )
//            )
//        );
//
//        $form->bind($data);
//        $form->get('submitBtn')->setAttribute('value', 'Save');
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            $filter = $this->getServiceLocator()->get('FcFlight\Filter\LegFilter');
//            $form->setInputFilter($filter->getInputFilter());
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//                $data = $form->getData();
//                $refNumberOrder = $this->getLegModel()->save($data);
//                $this->flashMessenger()->addSuccessMessage("Data '"
//                . $refNumberOrder . "' was successfully saved.");
//                return $this->redirect()->toRoute('flights');
//            }
//        }
//
//        return array(
//            'id' => $id,getAirOperators
//            'form' => $form,
//        );
//    }

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
