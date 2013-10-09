<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\CityForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class CityController
 * @package FcLibraries\Controller
 */
class CityController extends AbstractActionController implements ControllerInterface
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

        $data = $this->CommonData()->getCityModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/cities',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_city')),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CityForm('city', array('countries' => $this->CommonData()->getCountries()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\CityFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->CommonData()->getCityModel()->add($filter);

                $message = "City '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getCityModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'city'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/city', array(
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
            return $this->redirect()->toRoute('zfcadmin/city', array(
                'action' => 'add'
            ));
        }
        $data = $this->CommonData()->getCityModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new CityForm('city', array('countries' => $this->CommonData()->getCountries()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\CountryFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->CommonData()->getCityModel()->save($data);

                $message = "City '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->CommonData()->getCityModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'city'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/cities');
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
            return $this->redirect()->toRoute('zfcadmin/cities');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->CommonData()->getCityModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string)$request->getPost('name');
                $this->CommonData()->getCityModel()->remove($id);

                $message = "City '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'city'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/cities');
        }

        return array(
            'id' => $id,
            'data' => $this->CommonData()->getCityModel()->get($id)
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
            'Country' => $data->country_name,
        );
    }
}
