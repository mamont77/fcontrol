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
     * @var integer
     */
    protected $_headerId = null;

    /**
     * @var integer
     */
    protected $_permissionId = null;

    /**
     * @var array
     */
    protected $_dataForLogger = array();

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'active'
            ));
        }

        $this->_setHeaderId($id);
        $refNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($this->_getHeaderId());
        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $permissions = $this->getPermissionModel()->getByHeaderId($this->_getHeaderId());



        $form = new PermissionForm('permission',
            array(
                'headerId' => $this->_getHeaderId(),
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
//                \Zend\Debug\Debug::dump($data);exit;

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
                        'id' => $this->_getHeaderId(),
                    ));
            }
        }
        return array('form' => $form,
            'headerId' => $this->_getHeaderId(),
            'headerStatus' => $headerStatus,
            'refNumberOrder' => $refNumberOrder,
            'permissions' => $permissions,
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
        $this->_setPermissionId($id);

        $refNumberOrder = $this->getPermissionModel()->getHeaderRefNumberOrderByPermissionId($this->_getPermissionId());
        $headerStatus = $this->redirectForDoneStatus($refNumberOrder);
        $data = $this->getPermissionModel()->get($this->_getPermissionId());
        $this->_setHeaderId((int)$data->headerId);
        $permissions = $this->getPermissionModel()->getByHeaderId($this->_getHeaderId());

        $this->_setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->_dataForLogger);

        $form = new PermissionForm('permission',
            array(
                'headerId' => $this->_getHeaderId(),
                'libraries' => array(
                    'airports' => $this->getParentLeg($this->_getHeaderId()),
                    'baseOfPermit' => $this->getBaseOfPermits(),
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

                $this->_setDataForLogger($this->getPermissionModel()->get($id));
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

        return array('form' => $form,
            'id' => $this->_getPermissionId(),
            'headerStatus' => $headerStatus,
            'refNumberOrder' => $refNumberOrder,
            'permissions' => $permissions,
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
        $this->_setPermissionId($id);

        $request = $this->getRequest();
        $refUri = $request->getHeader('Referer')->uri()->getPath();
        $refNumberOrder = $this->getPermissionModel()->getHeaderRefNumberOrderByPermissionId($this->_getPermissionId());
        $this->redirectForDoneStatus($refNumberOrder);

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->_setPermissionId($id);

                $loggerPlugin = $this->LogPlugin();
                $this->_setDataForLogger($this->getPermissionModel()->get($this->_getPermissionId()));
                $loggerPlugin->setOldLogRecord($this->_dataForLogger);

                $this->getPermissionModel()->remove($this->_getPermissionId());

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
            'id' => $this->_getPermissionId(),
            'referer' => $refUri,
            'refNumberOrder' => $refNumberOrder,
            'data' => $this->getPermissionModel()->get($this->_getPermissionId())
        );
    }

    /**
     * Get Agents List for twitter-typeahead field
     *
     * @return \Zend\Http\Response
     */
    public function getAgentsAction()
    {
        $data = $this->getKontragentModel()->fetchAll();

        $result = array();
        foreach ($data as $row) {
            $result[] = array(
                'id' => $row->id,
                'value' => $row->name,
                'name' => $row->name,
                'address' => $row->address,
                'mail' => $row->mail,
                'tokens' => array(
                    $row->name,
                    $row->short_name,
                    $row->mail,
                ),
            );
        }

        $view = new ViewModel(array(
            'data' => Json::encode($result),
        ));
        $view->setTerminal(true);

        return $view;
    }

    /**
     * Get LEGs List for twitter-typeahead field
     *
     * @return \Zend\Http\Response
     */
    public function getLegsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $data = $this->getLegModel()->getByHeaderId($id);

        $result = array();
        foreach ($data as $row) {
            $leg = $row['apDepIcao'] . ' (' . $row['apDepIata'] . ')' . ' ⇒ '
                . $row['apArrIcao'] . ' (' . $row['apArrIata'] . ')';

            $result[] = array(
                'id' => $row['id'],
                'value' => $leg,
                'name' => $leg,
                'tokens' => array(
                    $row['apDepIcao'],
                    $row['apDepIata'],
                    $row['apArrIcao'],
                    $row['apArrIata'],
                ),
            );
        }

        $view = new ViewModel(array(
            'data' => Json::encode($result),
        ));
        $view->setTerminal(true);

        return $view;
    }

    /**
     * Get Countries List for twitter-typeahead field
     *
     * @return \Zend\Http\Response
     */
    public function getCountriesAction()
    {
        $data = $this->getCountryModel()->fetchAll();

        $result = array();
        foreach ($data as $row) {
            $result[] = array(
                'id' => $row->id,
                'value' => $row->name,
                'name' => $row->name,
                'code' => $row->code,
                'tokens' => array(
                    $row->name,
                    $row->code,
                ),
            );
        }

        $view = new ViewModel(array(
            'data' => Json::encode($result),
        ));
        $view->setTerminal(true);

        return $view;
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
            'Permission' => $data->permission,
        );
    }

    /**
     * @param $id
     */
    protected function _setHeaderId($id)
    {
        $this->_headerId = $id;
    }

    /**
     * @return int
     */
    protected function _getHeaderId()
    {
        return $this->_headerId;
    }

    /**
     * @param $id
     */
    protected function _setPermissionId($id)
    {
        $this->_permissionId = $id;
    }

    /**
     * @return int
     */
    protected function _getPermissionId()
    {
        return $this->_permissionId;
    }
}
