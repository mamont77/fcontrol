<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable
{
    protected $userGateway;
    protected $roleGateway;

    public function __construct(TableGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->userGateway->select();
        return $resultSet;
    }

    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->userGateway->select(array('user_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'username' => $user->username,
            'email'  => $user->email,
            'display_name'  => $user->display_name,
            'password'  => $user->password,
            'state'  => $user->state,
        );

        $id = (int)$user->user_id;
        if ($id == 0) {
            $this->userGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->userGateway->update($data, array('user_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUser($id)
    {
        $this->userGateway->delete(array('user_id' => $id));
    }
}
