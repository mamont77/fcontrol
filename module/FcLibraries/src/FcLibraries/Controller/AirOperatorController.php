<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\AirOperatorForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class AirOperatorController
 * @package FcLibraries\Controller
 */
class AirOperatorController extends AbstractActionController implements ControllerInterface
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

        $data = $this->CommonData()->getAirOperatorModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/air_operators',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_air_operator')),
        ));
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $form = new AirOperatorForm('air_operator', array('countries' => $this->CommonData()->getCountries()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AirOperatorFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->CommonData()->getAirOperatorModel()->add($filter);

                $message = "Air Operator '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getAirOperatorModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'air operator'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/air_operator',
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
            return $this->redirect()->toRoute('zfcadmin/air_operator', array(
                'action' => 'add'
            ));
        }
        $data = $this->CommonData()->getAirOperatorModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new AirOperatorForm('air_operator', array('countries' => $this->CommonData()->getCountries()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AirOperatorFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->CommonData()->getAirOperatorModel()->save($data);

                $message = "Air Operator '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->CommonData()->getAirOperatorModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger)->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'air operator'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/air_operators');
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
            return $this->redirect()->toRoute('zfcadmin/air_operators');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getAirOperatorModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string) $request->getPost('name');
                $this->CommonData()->getAirOperatorModel()->remove($id);

                $message = "Air Operator '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'aircraft'));
                $logger->Warn($loggerPlugin->getLogMessage());

            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/air_operators');
        }

        return array(
            'id' => $id,
            'data' => $this->CommonData()->getAirOperatorModel()->get($id)
        );
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Name of Air Operator' => $data->name,
            'Short name of Air Operator' => $data->short_name,
            'Code ICAO' => $data->code_icao,
            'Code IATA' => $data->code_iata,
            'Country' => $data->country_name,
        );
    }
}
