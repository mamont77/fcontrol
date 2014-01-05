<?php
/**
 * @namespace
 */
namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\KontragentForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class KontragentController
 * @package FcLibraries\Controller
 */
class KontragentController extends IndexController
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

        $data = $this->getKontragentModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/kontragents',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_kontragent')),
        ));
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $form = new KontragentForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\KontragentFilter');

            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->getKontragentModel()->add($filter);

                $message = "Kontragent '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getKontragentModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'kontragent'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/kontragent',
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
            return $this->redirect()->toRoute('zfcadmin/kontragent', array(
                'action' => 'add'
            ));
        }
        $data = $this->getKontragentModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new KontragentForm();
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\KontragentFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getKontragentModel()->save($data);

                $message = "Kontragent '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getKontragentModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'kontragent'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/kontragents');
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
            return $this->redirect()->toRoute('zfcadmin/kontragents');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getKontragentModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string) $request->getPost('name');
                $this->getKontragentModel()->remove($id);

                $message = "Kontragent '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'kontragent'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/kontragents');
        }

        return array(
            'id' => $id,
            'data' => $this->getKontragentModel()->get($id)
        );
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'ID number' => $data->id,
            'Name of Kontragent' => $data->name,
            'Short name of Kontragent' => $data->short_name,
            'Address' => $data->address,
            'Phone 1' => $data->phone1,
            'Phone 2' => $data->phone2,
            'Phone 3' => $data->phone3,
            'Fax' => $data->fax,
            'E-mail' => $data->mail,
            'Agreement' => $data->agreement,
            'Term of payment' => $data->termOfPayment,
        );
    }
}
