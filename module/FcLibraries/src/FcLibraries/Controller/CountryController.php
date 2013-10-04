<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Form\CountryForm;
use FcLibrariesSearch\Form\SearchForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use FcLibraries\Controller\Plugin\LogPlugin as LogPlugin;

/**
 * Class CountryController
 * @package FcLibraries\Controller
 */
class CountryController extends AbstractActionController implements ControllerInterface
{

    /**
     * @var
     */
    protected $countryModel;

    /**
     * @var
     */
    protected $regionModel;

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

        $data = $this->CommonData()->getCountryModel()->fetchAll($select->order($order_by . ' ' . $order));
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
            'route' => 'zfcadmin/countries',
            'searchForm' => new SearchForm('librarySearch', array('library' => 'library_country')),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CountryForm('country', array('regions' => $this->CommonData()->getRegions()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\CountryFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $lastId = $this->CommonData()->getCountryModel()->add($filter);

                $message = "Country '" . $data['name'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->CommonData()->getCountryModel()->get($lastId));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'country'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/country', array(
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
            return $this->redirect()->toRoute('zfcadmin/country', array(
                'action' => 'add'
            ));
        }
        $data = $this->CommonData()->getCountryModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = new LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new CountryForm('country', array('regions' => $this->CommonData()->getRegions()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\RegionFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->CommonData()->getCountryModel()->save($data);

                $message = "Country '" . $data->name . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->CommonData()->getCountryModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'country'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('zfcadmin/countries');
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
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = new LogPlugin();
                $this->setDataForLogger($this->CommonData()->getCountryModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $name = (string)$request->getPost('name');
                $this->CommonData()->getCountryModel()->remove($id);

                $message = "Country '" . $name . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $this->getCurrentUserName(), 'component' => 'country'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        return array(
            'id' => $id,
            'data' => $this->CommonData()->getCountryModel()->get($id)
        );
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
            'Region' => $data->region_name,
            'Code' => $data->code,
        );
    }
}
