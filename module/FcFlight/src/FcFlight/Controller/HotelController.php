<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\HotelForm;

/**
 * Class HotelController
 * @package FcFlight\Controller
 */
class HotelController extends FlightController
{
    /**
     * @var int
     */
    protected $headerId;

    /**
     * @var array
     */
    protected $dataForLogger = array();

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->headerId = (int)$this->params()->fromRoute('id', 0);
        if (!$this->headerId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($this->headerId);

        $this->redirectForDoneStatus($refNumberOrder);


        $hotels = $this->getHotelModel()->getByHeaderId($this->headerId);

        $form = new HotelForm('hotel',
            array(
                'headerId' => $this->headerId,
                'libraries' => array(
                    'airports' => $this->getParentLeg($this->headerId),
                    'agents' => $this->getKontragents(),
                ),
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\HotelFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $data = $this->getHotelModel()->add($filter);

                $message = "Hotel was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getHotelModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'hotel'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('hotel',
                    array(
                        'action' => 'add',
                        'id' => $this->headerId,
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $this->headerId,
            'refNumberOrder' => $refNumberOrder,
            'hotels' => $hotels,
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

        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($id);
        $data = $this->getHotelModel()->get($id);
        $this->headerId = (int)$data->headerId;

        $this->redirectForDoneStatus($refNumberOrder);

        $hotels = $this->getHotelModel()->getByHeaderId($this->headerId);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new HotelForm('hotel',
            array(
                'headerId' => $this->headerId,
                'libraries' => array(
                    'airports' => $this->getParentLeg($this->headerId),
                    'agents' => $this->getKontragents(),
                ),
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\HotelFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $this->getHotelModel()->save($data);

                $message = "Hotel was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getHotelModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'hotel'));
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
            'hotels' => $hotels,
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
        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($id);

        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getHotelModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $this->getHotelModel()->remove($id);

                $message = "Hotel was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'hotel'));
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
            'data' => $this->getHotelModel()->get($id)
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
            'Need' => ($data->isNeed) ? 'YES' : 'NO',
            'Agent' => $data->kontragentShortName,
        );
    }
}
