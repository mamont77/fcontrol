<?php

namespace FcAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcAdmin\Model\User;
use FcAdmin\Model\Role;
use FcAdmin\Form\UserForm;

class UserController extends AbstractActionController
{
    protected $userTable;
    protected $roleTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'users' => $this->getUserTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new UserForm();

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
                $user->user_id = $this->getUserTable()->saveUser($user);
                $this->getRoleTable()->saveRole($user);

                // Redirect to list of users
                return $this->redirect()->toRoute('zfcadmin/users');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('zfcadmin/users', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserTable()->getUser($id);

        $form  = new UserForm();
        $form->bind($user);
        $form->get('role_id')->setValue($user->role_id);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $userData = $form->getData();
                $userModel = new User();
                $userData->password = $userModel->changePassword($userData->password);
                $this->getUserTable()->saveUser($form->getData());
                $this->getRoleTable()->saveRole($form->getData());

                // Redirect to list of users
                return $this->redirect()->toRoute('zfcadmin/users');
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
            return $this->redirect()->toRoute('zfcadmin/users');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'Нет');

            if ($del == 'Да') {
                $id = (int) $request->getPost('user_id');
                $this->getUserTable()->deleteUser($id);
                $this->getRoleTable()->deleteRole($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('zfcadmin/users');
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
            $this->userTable = $sm->get('FcAdmin\Model\UserTable');
        }
        return $this->userTable;
    }

    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('FcAdmin\Model\RoleTable');
        }
        return $this->roleTable;
    }

}
