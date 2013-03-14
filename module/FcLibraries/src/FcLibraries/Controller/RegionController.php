<?php

namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use FcLibraries\Model\Region;
use FcLibraries\Form\RegionForm;


class RegionController extends AbstractActionController implements ControllerInterface
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $form = new RegionForm();
        $form->get('submit')->setValue('Добавить');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new Region();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $userData = $form->getData();
                $userModel = new Region();
                $user->exchangeArray($userData);
                $user->user_id = $this->getUserTable()->saveUser($user);

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
