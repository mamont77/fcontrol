<?php
/**
 * @namespace
 */
namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\TypeOfApServiceForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class TypeOfApServiceController
 * @package FcLibraries\Controller
 */
class TypeOfApServiceController extends IndexController
{
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

        $data = $this->getTypeOfApServiceModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/type_of_ap_services',
        ));
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $form = new TypeOfApServiceForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\TypeOfApServiceFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->getTypeOfApServiceModel()->add($filter);

                $message = "Type of AP Service '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getTypeOfApServiceModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'type_of_ap_service'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/type_of_ap_service',
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
            return $this->redirect()->toRoute('zfcadmin/type_of_ap_service', array(
                'action' => 'add'
            ));
        }
        $data = $this->getTypeOfApServiceModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new TypeOfApServiceForm();
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\TypeOfApServiceFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getTypeOfApServiceModel()->save($data);

                $message = "Type of AP Service '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getTypeOfApServiceModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'type_of_ap_service'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/type_of_ap_services');
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
            return $this->redirect()->toRoute('zfcadmin/type_of_ap_services');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getTypeOfApServiceModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string) $request->getPost('name');
                $this->getTypeOfApServiceModel()->remove($id);

                $message = "Type of AP Service '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'type_of_ap_service'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/type_of_ap_services');
        }

        return array(
            'id' => $id,
            'data' => $this->getTypeOfApServiceModel()->get($id)
        );
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