<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\LegForm;
use Zend\Json\Json as Json;


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
        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($headerId);
        $lastLeg = end($legs);
        if ($lastLeg) {
            $previousDate = $lastLeg['dateOfFlight'];
            $previousApArrCountryId = $lastLeg['apArrCountryId'];
            $previousApArrAirportId = $lastLeg['apArrAirportId'];
        } else {
            $previousDate = null;
            $previousApArrCountryId = null;
            $previousApArrAirportId = null;
        }

        $form = new LegForm('leg',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'flightNumberAirports' => $this->getAirOperators(),
                    'countries' => $this->getCountries(),
                ),
                'previousValues' => array(
                    'previousDate' => $previousDate,
                    'preSelected' => array(
                        'apDepCountryId' => $previousApArrCountryId,
                        'apDepAirportId' => $previousApArrAirportId,
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
            'headerStatus' => $headerStatus,
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
        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $data = $this->getLegModel()->get($id);
        $legs = $this->getLegModel()->getByHeaderId($data->headerId);
        $lastLeg = end($legs);

        if ($lastLeg) {
            $previousDate = $lastLeg['dateOfFlight'];
            $preSelectedApDep = $lastLeg['apArrAirportId'];
        } else {
            $previousDate = null;
            $preSelectedApDep = null;
        }

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $data->flightNumber['flightNumberAirportId'] = $data->flightNumberAirportId;
        $data->flightNumber['flightNumberText'] = $data->flightNumberText;
        $data->apDep['apDepAirportId'] = $data->apDepAirportId;
        $data->apDep['apDepTime'] = $data->apDepTime;
        $data->apArr['apArrAirportId'] = $data->apArrAirportId;
        $data->apArr['apArrTime'] = $data->apArrTime;

        $form = new LegForm('leg',
            array(
                'libraries' => array(
                    'flightNumberAirports' => $this->getAirOperators(),
                    'countries' => $this->getCountries(),
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
            'headerStatus' => $headerStatus,
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
     * @return \Zend\Http\Response
     */
    public function getAirportsAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $data = $this->getAirportModel()->getByCountryId($id);

        $result = array(
            'countryId' => $id,
            'airports' => array(),
        );
        foreach ($data as $row) {
            $result['airports']['id_' . $row->id]['name'] = $row->name;
            $result['airports']['id_' . $row->id]['code'] = $row->code_icao . ' (' . $row->code_iata . ')';
        }
        uasort($result['airports'], array($this, 'sortLibrary'));

        $view = new ViewModel(array(
            'data' => Json::encode($result),
        ));

        $view->setTerminal(true);

        return $view;
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
