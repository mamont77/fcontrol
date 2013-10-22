<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\LegForm;

/**
 * Class LegController
 * @package FcFlight\Controller
 */
class LegController extends FlightController
{
    /**
     * @var array
     */
    protected $dataForLogger = array();

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
                $data = $this->getLegModel()->add($filter);

                $message = "Leg '" . $data['hash'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getLegModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'leg'));
                $logger->Info($loggerPlugin->getLogMessage());

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
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getLegModel()->getHeaderRefNumberOrderByLegId($id);
        $this->redirectForDoneStatus($refNumberOrder);

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

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

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

                $message = "Leg '" . $summaryData . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getLegModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'leg'));
                $logger->Notice($loggerPlugin->getLogMessage());

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
        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getLegModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $id = (int)$request->getPost('id');
                $this->getLegModel()->remove($id);

                $message = "Leg was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'leg'));
                $logger->Warn($loggerPlugin->getLogMessage());
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

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Date of Flight' => $data->dateOfFlight,
            'Flight Number (ICAO/IATA/Text)' => $data->flightNumberIcao . '/'
                . $data->flightNumberIata . '/' . $data->flightNumberText,
            'Ap Dep (ICAO/IATA/Time)' => $data->apDepIcao . '/'
                . $data->apDepIata . '/' . $data->apDepTime,
            'Ap Arr (ICAO/IATA/Time)' => $data->apArrIcao . '/'
                . $data->apArrIata . '/' . $data->apArrTime,
        );
    }
}
