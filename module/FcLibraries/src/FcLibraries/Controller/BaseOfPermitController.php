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
     * @var
     */
    protected $baseOfPermitModel;

    /**
     * @var
     */
    protected $countryModel;

    /**
     * @var
     */
    protected $airportModel;


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

        $data = $this->getBaseOfPermitModel()->fetchAll($select->order($order_by . ' ' . $order));
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
        $form = new BaseOfPermitForm('base_of_permit', array('countries' => $this->getCountries()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\BaseOfPermitFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $this->getBaseOfPermitModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Base of Permit was successfully added.");
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
            return $this->redirect()->toRoute('zfcadmin/airport', array(
                'action' => 'add'
            ));
        }
        $data = $this->getAirportModel()->get($id);

        $form = new AirportForm('airport', array('cities' => $this->getCities()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AirportFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getAirportModel()->save($data);
                $this->flashMessenger()->addSuccessMessage("Airport '"
                . $data->name . "' was successfully saved.");
                return $this->redirect()->toRoute('zfcadmin/airports');
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
            return $this->redirect()->toRoute('zfcadmin/airports');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $name = (string)$request->getPost('name');
                $this->getAirportModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Airport '"
                . $name . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/airports');
        }

        return array(
            'id' => $id,
            'data' => $this->getAirportModel()->get($id)
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

        $data = $this->getAirportModel()->getByCountryId($id);

        $result = array(
            'countryId' => $id,
            'airports' => array(),
        );
        foreach ($data as $row) {
            $result['airports']['id_' . $row->id] = $row->name;
        }
        uasort($result['airports'], array($this, 'sortLibrary'));

        $view = new ViewModel(array(
            'data' => Json::encode($result),
        ));

        $view->setTerminal(true);

        return $view;
    }

    /**
     * @return array|object
     */
    private function getBaseOfPermitModel()
    {
        if (!$this->baseOfPermitModel) {
            $sm = $this->getServiceLocator();
            $this->baseOfPermitModel = $sm->get('FcLibraries\Model\BaseOfPermitModel');
        }
        return $this->baseOfPermitModel;
    }

    /**
     * @return array|object
     */
    public function getCountryModel()
    {
        if (!$this->countryModel) {
            $sm = $this->getServiceLocator();
            $this->countryModel = $sm->get('FcLibraries\Model\CountryModel');
        }
        return $this->countryModel;
    }

    /**
     * @return array|object
     */
    private function getAirportModel()
    {
        if (!$this->airportModel) {
            $sm = $this->getServiceLocator();
            $this->airportModel = $sm->get('FcLibraries\Model\AirportModel');
        }
        return $this->airportModel;
    }

    /**
     * @return mixed
     */
    private function getCountries()
    {
        return $this->getCountryModel()->fetchAll();
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
