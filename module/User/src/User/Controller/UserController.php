<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use User\Form\UserForm;

class UserController extends AbstractActionController
{
    protected $userTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'users' => $this->getUserTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Добавить');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $userData = $form->getData();
                $userModel = new User();
                $userData['password'] = $userModel->changePassword($userData['password']);
                $user->exchangeArray($userData);
                $this->getUserTable()->saveUser($user);

                // Redirect to list of users
                return $this->redirect()->toRoute('admin/users');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/users', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserTable()->getUser($id);
        $form  = new UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Сохранить');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUserTable()->saveUser($form->getData());

                // Redirect to list of users
                return $this->redirect()->toRoute('admin/users');
            }
        }

        return array(
            'user_id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/users');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'Нет');

            if ($del == 'Да') {
                $id = (int) $request->getPost('user_id');
                $this->getUserTable()->deleteUser($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('admin/users');
        }

        return array(
            'user_id' => $id,
            'user' => $this->getUserTable()->getUser($id)
        );
    }

    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
}
