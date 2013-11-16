<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\ApServiceForm;

/**
 * Class ApServiceController
 * @package FcFlight\Controller
 */
class ApServiceController extends FlightController
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
        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($headerId);
        $apServices = $this->getApServiceModel()->getByHeaderId($headerId);

        $airports = array();
        $legsCopy = $legs;
        $legFirst = reset($legs);
        $airports[$legFirst['id'] . '-' . $legFirst['apDepAirportId']] = $legFirst['apDepIcao'] . ' (' . $legFirst['apDepIata'] . '): '
            . $legFirst['dateOfFlight'] . ' ' . $legFirst['apDepTime'];
        foreach ($legs as $leg) {
            $nextLeg = next($legsCopy);
            $selectionValues = $leg['apArrIcao'] . ' (' . $leg['apArrIata'] . '): '
                . $leg['dateOfFlight'] . ' ' . $leg['apArrTime'];

            if (!is_bool($nextLeg)) {
                $selectionValues .= ' ⇒ ' . $nextLeg['dateOfFlight'] . ' ' . $nextLeg['apDepTime']; //✈
            }

            $airports[$leg['id'] . '-' . $leg['apArrAirportId']] = $selectionValues;
        }

        $form = new ApServiceForm('apService',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'airports' => $airports,
                    'typeOfApServices' => $this->getTypeOfApServices(),
                    'agents' => $this->getKontragents(),
                ),
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\ApServiceFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $data = $this->getApServiceModel()->add($filter);

                $message = "ApService was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getApServiceModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'apService'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('apService',
                    array(
                        'action' => 'add',
                        'id' => $headerId,
                    ));
            }
        }
        return array(
            'header' => $header,
            'legs' => $legs,
            'apServices' => $apServices,
            'form' => $form,
        );
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $serviceId = (int)$this->params()->fromRoute('id', 0);
        if (!$serviceId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getApServiceModel()->getHeaderRefNumberOrderByApServiceId($serviceId);

        $data = $this->getApServiceModel()->get($serviceId);
        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($header->id);

        $this->redirectForDoneStatus($refNumberOrder);
        $apServices = $this->getApServiceModel()->getByHeaderId($header->id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new ApServiceForm('apService',
            array(
                'headerId' => $header->id,
                'libraries' => array(
                    'airports' => $this->getParentLeg($header->id),
                    'agents' => $this->getKontragents(),
                ),
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\ApServiceFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $this->getApServiceModel()->save($data);

                $message = "ApService was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getApServiceModel()->get($serviceId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'apService'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }

        return array(
            'id' => $data->id,
            'header' => $header,
            'legs' => $legs,
            'apServices' => $apServices,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $serviceId = (int)$this->params()->fromRoute('id', 0);
        if (!$serviceId) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        $refUri = $request->getHeader('Referer')->uri()->getPath();
        $refNumberOrder = $this->getApServiceModel()->getHeaderRefNumberOrderByApServiceId($serviceId);

        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $serviceId = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getApServiceModel()->get($serviceId));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $this->getApServiceModel()->remove($serviceId);

                $message = "ApService was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'apService'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            $redirectPath = (string)$request->getPost('referer');
            // Redirect to back
            return $this->redirect()->toUrl($redirectPath);
        }

        return array(
            'id' => $serviceId,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getApServiceModel()->get($serviceId)
        );
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Airport' => $data->airportName . ' (' . $data->icao . '/' . $data->iata . ')',
            'Type of AP Service' => $data->typeOfApServiceName,
            'Agent' => $data->kontragentShortName,
            'Price' => $data->price,
            'Currency' => $data->currency,
            'Exchange Rate' => $data->exchangeRate,
            'Price USD' => $data->priceUSD,
        );
    }
}
