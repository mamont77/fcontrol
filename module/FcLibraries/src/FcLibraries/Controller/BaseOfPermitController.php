<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\BaseOfPermitForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Json\Json as Json;

/**
 * Class BaseOfPermitController
 * @package FcLibraries\Controller
 */
class BaseOfPermitController extends AbstractActionController implements ControllerInterface
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
            $this->params()->fromRoute('order_by') : 'airportName';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $data = $this->CommonData()->getBaseOfPermitModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/base_of_permits',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_base_of_permit')),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new BaseOfPermitForm('base_of_permit', array('countries' => $this->CommonData()->getCountries()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\BaseOfPermitFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->CommonData()->getBaseOfPermitModel()->add($filter);

                $message = "Base of Permit was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getBaseOfPermitModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'base of permit'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/base_of_permit', array(
                    'action' => 'add'
                ));
            }
        }
        return array('form' => $form);
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/base_of_permit', array(
                'action' => 'add'
            ));
        }
        $data = $this->CommonData()->getBaseOfPermitModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new BaseOfPermitForm('base_of_permit', array('countries' => $this->CommonData()->getCountries()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\BaseOfPermitFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->CommonData()->getBaseOfPermitModel()->save($data);

                $message = "Base of Permit was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->CommonData()->getBaseOfPermitModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'base of permit'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/base_of_permits');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/base_of_permits');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getBaseOfPermitModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string)$request->getPost('name');
                $this->CommonData()->getBaseOfPermitModel()->remove($id);

                $message = "Base of Permit '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'base of permit'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/base_of_permits');
        }

        return array(
            'id' => $id,
            'data' => $this->CommonData()->getBaseOfPermitModel()->get($id)
        );
    }

    /**
     * @return \Zend\Http\Response
     */
    public function getAirportsAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/base_of_permit', array(
                'action' => 'add'
            ));
        }

        $data = $this->CommonData()->getAirportModel()->getByCountryId($id);

        $result = array(
            'countryId' => $id,
            'airports' => array(),
        );
        foreach ($data as $row) {
            $result['airports']['id_' . $row->id] = $row->name;
        }
        uasort($result['airports'], array($this->CommonData(), 'sortLibrary'));

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
            'Airport' => $data->airportName,
            'City' => $data->cityName,
            'Country' => $data->countryName,
            'Term validity' => $data->termValidity,
            'Term to take' => $data->termToTake,
            'Info to take' => $data->infoToTake,
        );
    }
}
