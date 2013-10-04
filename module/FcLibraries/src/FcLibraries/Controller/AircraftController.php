<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\AircraftForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use FcLibraries\Controller\Plugin\LogPlugin as LogPlugin;

/**
 * Class AircraftController
 * @package FcLibraries\Controller
 */
class AircraftController extends AbstractActionController implements ControllerInterface
{
    /**
     * @var
     */
    protected $aircraftModel;

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

        $data = $this->getAircraftModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/aircrafts',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_aircraft')),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new AircraftForm('aircraft', array('aircraft_types' => $this->CommonData()->getAircraftTypes()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AircraftFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->getAircraftModel()->add($filter);

                $message = "Aircraft '" . $data['reg_number'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->getAircraftModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'aircraft'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/aircraft', array(
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
            return $this->redirect()->toRoute('zfcadmin/aircraft', array(
                'action' => 'add'
            ));
        }

        $data = $this->getAircraftModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = new LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new AircraftForm('aircraft', array('aircraft_types' => $this->CommonData()->getAircraftTypes()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AircraftFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $this->getAircraftModel()->save($data);

                $message = "Aircraft '" . $data->reg_number . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getAircraftModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'aircraft'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/aircrafts');
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
            return $this->redirect()->toRoute('zfcadmin/aircrafts');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->getAircraftModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $reg_number = (string)$request->getPost('reg_number');
                $this->getAircraftModel()->remove($id);

                $message = "Aircraft '" . $reg_number . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'aircraft'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/aircrafts');
        }

        return array(
            'id' => $id,
            'data' => $this->getAircraftModel()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getAircraftModel()
    {
        if (!$this->aircraftModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftModel = $sm->get('FcLibraries\Model\AircraftModel');
        }
        return $this->aircraftModel;
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
            'Type Aircraft' => $data->aircraft_type_name,
            'Reg Number' => $data->reg_number,
        );
    }
}
