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
 * Class UserTable
 * @package FcAdmin\Model
 */
class UserTable extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'user';

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User());

        $this->initialize();
    }

    /**
     * @param Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('user_id', 'email', 'username', 'state'));
        $select->join('user_role_linker',
            'user_role_linker.user_id = user.user_id',
            array('role_id'), 'left');
        $select->join('user_role',
            'user_role.id = user_role_linker.role_id',
            array('role_name' => 'role_id'), 'left');

//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getUser($id)
    {
        $id = (int)$id;

        $select = new Select;
        $select->from('user');
        $select->columns(array('user_id', 'email', 'username', 'state'));
        $select->where(array('user.user_id' => $id));
        $select->join('user_role_linker',
            'user_role_linker.user_id = user.user_id',
            array('role_id'), 'left');
        $select->join('user_role',
            'user_role.id = user_role_linker.role_id',
            array('role_name' => 'role_id'), 'left');

//        \Zend\Debug\Debug::dump($select->getSqlString());

        $rowSet = $this->selectWith($select);
        $row = $rowSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param User $user
     * @return int
     * @throws \Exception
     */
    public function saveUser(User $user)
    {
        $userData = array(
            'username' => $user->username,
            'email' => $user->email,
            'display_name' => $user->display_name,
            'password' => $user->password,
            'state' => $user->state,
        );

        $id = (int)$user->user_id;
        if ($id == 0) {
            $this->insert($userData);
        } else {
            if ($this->getUser($id)) {
                $this->update($userData, array('user_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return $this->getLastInsertValue();
    }

    /**
     * @param $id
     */
    public function deleteUser($id)
    {
        $this->delete(array('user_id' => $id));
    }
}
