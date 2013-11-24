<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\PermissionForm;
use Zend\Json\Json as Json;

/**
 * Class PermissionController
 * @package FcFlight\Controller
 */
class PermissionController extends FlightController
{
    /**
     * @var array
     */
    protected $_dataForLogger = array();

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
        $permissions = $this->getPermissionModel()->getByHeaderId($headerId);

        $form = new PermissionForm('permission',
            array(
                'headerId' => $headerId,
                'libraries' => array(
                    'agents' => $this->getKontragents(),
                    'legs' => $this->getLegModel()->getListByHeaderId($headerId),
                    'countries' => $this->getCountries(),
                    'typeOfPermissions' => $this->geTypeOfPermissions(),
                ),
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\PermissionFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $data = $this->getPermissionModel()->add($filter);

                $message = "Permission was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->_setDataForLogger($this->getPermissionModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->_dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'permission'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('permission',
                    array(
                        'action' => 'add',
                        'id' => $headerId,
                    ));
            }
        }
        return array(
            'header' => $header,
            'legs' => $legs,
            'permissions' => $permissions,
            'form' => $form,
        );
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $permissionId = (int)$this->params()->fromRoute('id', 0);
        if (!$permissionId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $refNumberOrder = $this->getPermissionModel()->getHeaderRefNumberOrderByPermissionId($permissionId);
        $this->redirectForDoneStatus($refNumberOrder);
        $data = $this->getPermissionModel()->get($permissionId);
        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        $legs = $this->getLegModel()->getByHeaderId($header->id);
        $permissions = $this->getPermissionModel()->getByHeaderId($header->id);

        $this->_setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->_dataForLogger);

        $form = new PermissionForm('permission',
            array(
                'headerId' => $header->id,
                'libraries' => array(
                    'agents' => $this->getKontragents(),
                    'legs' => $this->getLegModel()->getListByHeaderId($header->id),
                    'countries' => $this->getCountries(),
                    'typeOfPermissions' => $this->geTypeOfPermissions(),
                ),
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\PermissionFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $this->getPermissionModel()->save($data);

                $message = "Permission was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->_setDataForLogger($this->getPermissionModel()->get($permissionId));
                $loggerPlugin->setNewLogRecord($this->_dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'permission'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $refNumberOrder,
                    ));
            }
        }

        return array(
            'id' => $permissionId,
            'header' => $header,
            'legs' => $legs,
            'permissions' => $permissions,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $permissionId = (int)$this->params()->fromRoute('id', 0);
        if (!$permissionId) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        $refUri = $request->getHeader('Referer')->uri()->getPath();
        $refNumberOrder = $this->getPermissionModel()->getHeaderRefNumberOrderByPermissionId($permissionId);
        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $permissionId = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->_setDataForLogger($this->getPermissionModel()->get($permissionId));
                $loggerPlugin->setOldLogRecord($this->_dataForLogger);

                $this->getPermissionModel()->remove($permissionId);

                $message = "Permission was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'permission'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            $redirectPath = (string)$request->getPost('referer');
            // Redirect to back
            return $this->redirect()->toUrl($redirectPath);
        }

        return array(
            'id' => $permissionId,
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getPermissionModel()->get($permissionId)
        );
    }

    /**
     * @param $data
     */
    protected function _setDataForLogger($data)
    {
        $this->_dataForLogger = array(
            'id' => $data->id,
            'Agent' => $data->agentName,
            'LEG' => $data->airportDepartureICAO . ' (' . $data->airportDepartureIATA . ')'
                . ' ⇒ '
                . $data->airportArrivalICAO . ' (' . $data->airportArrivalIATA . ')',
            'Country' => $data->countryName,
            'Type of Permission' => $data->typeOfPermission,
            'Time of request' => $data->requestTime,
            'Permission' => $data->permission,
            'Comment' => $data->comment,
        );
    }
}
