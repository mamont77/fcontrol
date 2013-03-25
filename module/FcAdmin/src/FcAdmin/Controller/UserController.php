<?php

namespace FcAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcAdmin\Model\User;
use FcAdmin\Form\UserForm;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class UserController extends AbstractActionController
{
    protected $userTable;
    protected $roleTable;

    public function indexAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'username';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $albums = $this->getUserTable()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 5;

        $albums->current();
        $pagination = new Paginator(new paginatorIterator($albums));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'zfcadmin/users',
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
        $form->get('submitBtn')->setAttribute('value', 'Save');
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
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
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
