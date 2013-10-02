<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\UnitForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use FcLibraries\Controller\Plugin\LogPlugin as LogPlugin;

/**
 * Class UnitController
 * @package FcLibraries\Controller
 */
class UnitController extends AbstractActionController implements ControllerInterface
{

    /**
     * @var
     */
    protected $unitModel;

    /**
     * @var array
     */
    protected $dataForLogger = array();

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'name';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $data = $this->getUnitModel()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 20;

        $data->current();
        $pagination = new Paginator(new paginatorIterator($data));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'zfcadmin/units',
        ));
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $form = new UnitForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\UnitFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->getUnitModel()->add($filter);

                $message = "Unit '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->getUnitModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'unit'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/unit',
                    array(
                        'action' => 'add'
                    ));
            }
        }
        return array('form' => $form);
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/unit', array(
                'action' => 'add'
            ));
        }
        $data = $this->getUnitModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = new LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new UnitForm();
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\UnitFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getUnitModel()->save($data);

                $message = "Unit '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getUnitModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'unit'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/units');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * @return array
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/units');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->getUnitModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string) $request->getPost('name');
                $this->getUnitModel()->remove($id);

                $message = "Unit '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'unit'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/units');
        }

        return array(
            'id' => $id,
            'data' => $this->getUnitModel()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getUnitModel()
    {
        if (!$this->unitModel) {
            $sm = $this->getServiceLocator();
            $this->unitModel = $sm->get('FcLibraries\Model\UnitModel');
        }
        return $this->unitModel;
    }

    /**
     * Get the display name of the user
     *
     * @return mixed
     */
    public function getCurrentUserName()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->zfcUserAuthentication()->getIdentity()->getUsername();
        }
        return null;
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Name' => $data->name,
        );
    }
}
