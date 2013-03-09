<?php

namespace FcAdmin\Model;

use Zend\Db\TableGateway\TableGateway;

class RoleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getRole($userId)
    {
        $rowset = $this->tableGateway->select(array('user_id' => (int)$userId));
        return $rowset->current();
    }

    public function saveRole(User $user)
    {
        $userId = (int)$user->user_id;
        $roleId = (string)$user->role_id;
        if ($this->getRole($userId)) {
            $this->tableGateway->update(array('role_id' => $roleId), array('user_id' => $userId));
        } else {
            $this->tableGateway->insert(array('user_id' => $userId, 'role_id' => $roleId));
        }

    }

    public function deleteRole($userId)
    {
        $this->tableGateway->delete(array('user_id' => $userId));
    }
}
