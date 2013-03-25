<?php

namespace FcLibraries\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class RegionTable extends AbstractTableGateway implements ModelInterface
{
    protected $table = 'library_region';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Region());

        $this->initialize();
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    public function get($id)
    {
        $id = (int)$id;
        $rowSet = $this->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function existName($name)
    {
        $name = (string)$name;
        $rowSet = $this->select(array('name' => $name));

        return ($rowSet->current()) ? true : false;
    }

    public function add(Region $object)
    {
        $data = array(
            'name' => $object->name,
        );
        if ($this->existName($object->name)) {
            throw new \Exception("$object->name in the table exists");
        } else {
            $this->insert($data);
        }
    }

    public function save(Region $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

}
