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
 *
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

        $header = $this->getFlightHeaderModel()->get($headerId);
        $this->redirectForDoneStatus($header->refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($headerId);
        $lastLeg = end($legs);
//        \Zend\Debug\Debug::dump($lastLeg);

        if ($lastLeg) {
            $previousDate = $lastLeg['apArrTime'];
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
                    'airOperators' => $this->getAirOperators(),
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
            'header' => $header,
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

        $data = $this->getLegModel()->get($id);

        $header = $this->getFlightHeaderModel()->get($data->headerId);
        $this->redirectForDoneStatus($header->refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($data->headerId);

        // try find previous LEG
        $legsCopy = array_values($legs);
        $tempKey = 0;

        foreach ($legsCopy as $key => $leg) {
            if ($leg['id'] == $id) {
                $tempKey = $key - 1;
            }
        }
        $previousLeg = $legsCopy[$tempKey];

        // try find next LEG
        rsort($legsCopy);
        $tempKey = 0;

        foreach ($legsCopy as $key => $leg) {
            if ($leg['id'] == $id) {
                $tempKey = $key - 1;
            }
        }
        $nextLeg = $legsCopy[$tempKey];
//        \Zend\Debug\Debug::dump($nextLeg);
//        \Zend\Debug\Debug::dump($previousLeg);


        if ($previousLeg) {
            $previousDate = $previousLeg['apArrTime'];
            $previousApArrCountryId = $previousLeg['apArrCountryId'];
            $previousApArrAirportId = $previousLeg['apArrAirportId'];
        } else {
            $previousDate = null;
            $previousApArrCountryId = null;
            $previousApArrAirportId = null;
        }
//        \Zend\Debug\Debug::dump($previousDate);
//        \Zend\Debug\Debug::dump($previousApArrCountryId);
//        \Zend\Debug\Debug::dump($previousApArrAirportId);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new LegForm('leg',
            array(
                'libraries' => array(
                    'airOperators' => $this->getAirOperators(),
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

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {

            $filter = $this->getServiceLocator()->get('FcFlight\Filter\LegFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                // Save current LEG
                $summaryData = $this->getLegModel()->save($data);

                // Save apDepAirportId in next LEG
                if ($nextLeg['id']) {
                    $nextLeg = $this->getLegModel()->get($nextLeg['id']);
                    $nextLeg->apDepAirportId = $data->apArrAirportId;
                    $this->getLegModel()->save($nextLeg);
                }

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
                        'refNumberOrder' => $header->refNumberOrder,
                    ));
            }
        }

        return array('form' => $form,
            'id' => $data->id,
            'header' => $header,
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
        $refNumberOrder = $this->getLegModel()->getHeaderRefNumberOrderByLegId($id);

        // мы не можем удалить leg, если за ним идет цепочка строк
        if (!$this->getLegModel()->legIsLast($id)) {
            $this->flashMessenger()->addErrorMessage('This LEG is not the last.');
            return $this->redirect()->toRoute('browse',
                array(
                    'action' => 'show',
                    'refNumberOrder' => $refNumberOrder,
                ));
        }


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

            return $this->redirect()->toRoute('browse',
                array(
                    'action' => 'show',
                    'refNumberOrder' => $refNumberOrder,
                ));
        }

        return array(
            'id' => $id,
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
            $result['airports']['id_' . $row->id]['code'] = $row->code_icao . ' / ' . $row->code_iata . ' / ' . $row->short_name;
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
            'Flight #' => $data->flightNumber,
            'Ap Dep (ICAO/IATA/Time)' => $data->apDepIcao . '/'
                . $data->apDepIata . '/' . $data->apDepTime,
            'Ap Arr (ICAO/IATA/Time)' => $data->apArrIcao . '/'
                . $data->apArrIata . '/' . $data->apArrTime,
        );
    }
}
