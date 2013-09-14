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
     * @var
     */
    protected $airOperatorModel;

    /**
     * @var
     */
    protected $countryModel;

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

        $data = $this->getAirOperatorModel()->fetchAll($select->order($order_by . ' ' . $order));
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
        $form = new AirOperatorForm('air_operator', array('countries' => $this->getCountries()));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AirOperatorFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $this->getAirOperatorModel()->add($filter);
                $this->flashMessenger()->addSuccessMessage("Air Operator '"
                        . $data['name'] . "' was successfully added.");
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
        $data = $this->getAirOperatorModel()->get($id);

        $form = new AirOperatorForm('air_operator', array('countries' => $this->getCountries()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibraries\Filter\AirOperatorFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getAirOperatorModel()->save($data);
                $this->flashMessenger()->addSuccessMessage("Air Operator '"
                        . $data->name . "' was successfully saved.");
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
                $name = (string) $request->getPost('name');
                $this->getAirOperatorModel()->remove($id);
                $this->flashMessenger()->addSuccessMessage("Air Operator '"
                        . $name . "' was successfully deleted.");
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/air_operators');
        }

        return array(
            'id' => $id,
            'data' => $this->getAirOperatorModel()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getAirOperatorModel()
    {
        if (!$this->airOperatorModel) {
            $sm = $this->getServiceLocator();
            $this->airOperatorModel = $sm->get('FcLibraries\Model\AirOperatorModel');
        }
        return $this->airOperatorModel;
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
     * @return mixed
     */
    private function getCountries()
    {
        return $this->getCountryModel()->fetchAll();
    }
}
