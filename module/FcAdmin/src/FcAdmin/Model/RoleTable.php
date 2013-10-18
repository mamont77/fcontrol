<?php
/**
 * @namespace
 */
namespace FcAdmin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class RoleTable
 * @package FcAdmin\Model
 */
class RoleTable extends AbstractTableGateway
{
    protected $table = 'user_role_linker';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User());

        $this->initialize();
    }

    public function getRole($userId)
    {
        $rowset = $this->select(array('user_id' => (int)$userId));
        return $rowset->current();
    }

    public function saveRole(User $user)
    {
        $userId = (int)$user->user_id;
        $roleId = (string)$user->role_id;
        if ($this->getRole($userId)) {
            $this->update(array('role_id' => $roleId), array('user_id' => $userId));
        } else {
            $this->insert(array('user_id' => $userId, 'role_id' => $roleId));
        }

    }

    public function deleteRole($userId)
    {
        $this->delete(array('user_id' => $userId));
    }
}
