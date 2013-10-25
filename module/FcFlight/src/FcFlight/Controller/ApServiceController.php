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
        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $apServices = $this->getApServiceModel()->getByHeaderId($this->headerId);

        $form = new ApServiceForm('apService',
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
                        'id' => $this->headerId,
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $this->headerId,
            'headerStatus' => $headerStatus,
            'refNumberOrder' => $refNumberOrder,
            'apServices' => $apServices,
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

        $refNumberOrder = $this->getApServiceModel()->getHeaderRefNumberOrderByApServiceId($id);

        $data = $this->getApServiceModel()->get($id);
        $this->headerId = (int)$data->headerId;

        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $apServices = $this->getApServiceModel()->getByHeaderId($this->headerId);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new ApServiceForm('apService',
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
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\ApServiceFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $this->getApServiceModel()->save($data);

                $message = "ApService was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getApServiceModel()->get($id));
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

        return array('form' => $form,
            'id' => $data->id,
            'headerStatus' => $headerStatus,
            'refNumberOrder' => $refNumberOrder,
            'apServices' => $apServices,
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
        $refNumberOrder = $this->getApServiceModel()->getHeaderRefNumberOrderByApServiceId($id);

        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getApServiceModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $this->getApServiceModel()->remove($id);

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
            'id' => $id,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getApServiceModel()->get($id)
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
