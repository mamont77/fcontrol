<?php

namespace FcAdmin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class UserTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new Select;
        $select->from('user');
        $select->columns(array('user_id', 'email', 'username', 'state'));
        $select->join('user_role_linker', "user_role_linker.user_id = user.user_id", array('role_id'), 'left');

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getUser($id)
    {
        $id = (int)$id;

        $select = new Select;
        $select->from('user');
        $select->columns(array('user_id', 'email', 'username', 'state'));
        $select->where(array('user.user_id' => $id));
        $select->join('user_role_linker', "user_role_linker.user_id = user.user_id", array('role_id'), 'left');

        $rowSet = $this->tableGateway->selectWith($select);
        $row = $rowSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveUser(User $user)
    {
        $userData = array(
            'username' => $user->username,
            'email' => $user->email,
            'display_name' => $user->display_name,
            'password' => $user->password,
            'state' => $user->state,
        );
        $roleData = $user->role_id;

        $id = (int)$user->user_id;
        if ($id == 0) {
            $this->tableGateway->insert($userData);
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($userData, array('user_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return $this->tableGateway->getLastInsertValue();
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('user_id' => $id));
    }
}
