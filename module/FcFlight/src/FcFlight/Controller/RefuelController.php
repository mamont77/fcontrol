<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\RefuelForm;

/**
 * Class RefuelController
 * @package FcFlight\Controller
 */
class RefuelController extends FlightController
{

    /**
     * @var array
     */
    public $dataForLogger = array();

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {

        $headerId = (int)$this->params()->fromRoute('id', 0);
        if (!$headerId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($headerId);
        $this->redirectForDoneStatus($refNumberOrder);
        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($headerId);
        $refuels = $this->getRefuelModel()->getByHeaderId($headerId);
        $builtAirports = $this->buildAirportsFromLeg($legs);

        $refuelsTotal = 0;
        foreach ($refuels as &$refuel) {
            $refuelsTotal += $refuel['totalPriceUsd'];
            $builtId = $refuel['legId'] . '-' . $refuel['airportId'];
            if (array_key_exists($builtId, $builtAirports)) {
                $refuel['builtAirportName'] = $builtAirports[$builtId];
            }
        }

//        $lastRefuel = end($refuels);
//        if ($lastRefuel) {
//            $previousDate = $lastRefuel['date'];
//        } else {
//            $previousDate = null;
//        }

        $form = new RefuelForm('refuel',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'airports' => $builtAirports,
                    'agents' => $this->getKontragents(),
                    'units' => $this->getUnits(),
                ),
                'previousValues' => array(
//                    'previousDate' => $previousDate,
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

                $message = "Refuel '" . $data['hash'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getRefuelModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'refuel'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('refuel',
                    array(
                        'action' => 'add',
                        'id' => $headerId,
                    ));
            }
        }
        return array(
            'header' => $header,
            'legs' => $legs,
            'refuels' => $refuels,
            'refuelsTotal' => $refuelsTotal,
            'form' => $form,
        );
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $refuelId = (int)$this->params()->fromRoute('id', 0);
        if (!$refuelId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getRefuelModel()->getHeaderRefNumberOrderByRefuelId($refuelId);
        $this->redirectForDoneStatus($refNumberOrder);
        $data = $this->getRefuelModel()->get($refuelId);
        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($header->id);
        $refuels = $this->getRefuelModel()->getByHeaderId($header->id);
        $builtAirports = $this->buildAirportsFromLeg($legs);

        $refuelsTotal = 0;
        foreach ($refuels as &$refuel) {
            $refuelsTotal += $refuel['totalPriceUsd'];
            $builtId = $refuel['legId'] . '-' . $refuel['airportId'];
            if (array_key_exists($builtId, $builtAirports)) {
                $refuel['builtAirportName'] = $builtAirports[$builtId];
            }
        }

//        $lastRefuel = end($refuels);
//        if ($lastRefuel) {
//            $previousDate = $lastRefuel['date'];
//        } else {
//            $previousDate = null;
//        }

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new RefuelForm('refuel',
            array(
                'libraries' => array(
                    'airports' => $builtAirports,
                    'agents' => $this->getKontragents(),
                    'units' => $this->getUnits(),
                ),
                'previousValues' => array(
//                    'previousDate' => $previousDate,
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

                $message = "Refuel '" . $summaryData . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getRefuelModel()->get($refuelId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'refuel'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }

        return array(
            'id' => $refuelId,
            'header' => $header,
            'legs' => $legs,
            'refuels' => $refuels,
            'refuelsTotal' => $refuelsTotal,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $refuelId = (int)$this->params()->fromRoute('id', 0);
        if (!$refuelId) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        $refUri = $request->getHeader('Referer')->uri()->getPath();
        $refNumberOrder = $this->getRefuelModel()->getHeaderRefNumberOrderByRefuelId($refuelId);
        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $refuelId = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getRefuelModel()->get($refuelId));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $this->getRefuelModel()->remove($refuelId);

                $message = "Refuel was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'refuel'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            $redirectPath = (string)$request->getPost('referer');
            // Redirect to back
            return $this->redirect()->toUrl($redirectPath);
        }

        return array(
            'id' => $refuelId,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getRefuelModel()->get($refuelId)
        );
    }

    /**
     * @param $data
     */

    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Agent' => $data->agentName,
            'LEG' => $data->airportDepartureICAO . ' (' . $data->airportDepartureIATA . ')'
                . ' ⇒ '
                . $data->airportArrivalICAO . ' (' . $data->airportArrivalIATA . ')',
            'Quantity LTR' => $data->quantityLtr,
            'Unit' => $data->unitName,
            'Price USD' => $data->priceUsd,
            'Total USD' => $data->totalPriceUsd,
            'Date' => $data->date,
        );
    }
}
