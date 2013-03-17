<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\Airport;
use FcLibraries\Form\AirportForm;
use FcLibraries\Form\AirportFormInputFilter;

class AirportController extends AbstractActionController implements ControllerInterface
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $form = new AirportForm();
        $form->get('submit')->setValue('Добавить');

        $request = $this->getRequest();
        if ($request->isPost()) {
//            $model = new Airport();
            $filter = new AirportFormInputFilter();
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $model = new Airport();
                $model->exchangeArray($data);
                $model->id = $model->save($model);

                // Redirect to list of users
                return $this->redirect()->toRoute('zfcadmin/users');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }
}
