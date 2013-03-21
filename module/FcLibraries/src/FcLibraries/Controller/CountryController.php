<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Country;
use FcLibraries\Form\CountryForm;

class CountryController extends AbstractActionController implements ControllerInterface
{
    protected $_countryTable;
    protected $_regionTable;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'data' => $this->getCountryTable()->fetchAll(),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CountryForm('country', array('regions' => $this->getRegions()));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = new Country();
            $form->setInputFilter($model->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $model->exchangeArray($form->getData());
                $this->getCountryTable()->add($model);
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
        $data = $this->getCountryTable()->get($id);

        $form = new CountryForm('country', array('regions' => $this->getRegions()));
        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($data->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getCountryTable()->update($form->getData());

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
                $this->getCountryTable()->remove($id);
            }

            // Redirect to list
            return $this->redirect()->toRoute('zfcadmin/countries');
        }

        return array(
            'id' => $id,
            'data' => $this->getCountryTable()->get($id)
        );
    }

    /**
     * @return array|object
     */
    public function getCountryTable()
    {
        if (!$this->_countryTable) {
            $sm = $this->getServiceLocator();
            $this->_countryTable = $sm->get('FcLibraries\Model\CountryTable');
        }
        return $this->_countryTable;
    }

    /**
     * @return array|object
     */
    private function getRegionTable()
    {
        if (!$this->_regionTable) {
            $sm = $this->getServiceLocator();
            $this->_regionTable = $sm->get('FcLibraries\Model\RegionTable');
        }
        return $this->_regionTable;
    }

    /**
     * @return mixed
     */
    private function getRegions() {
        return $this->getRegionTable()->fetchAll();
    }
}
