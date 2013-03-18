<?php

namespace FcLibraries\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class RegionTable implements ModelInterface
{
    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->_tableGateway->select();
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int)$id;
        $rowSet = $this->_tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function issetName($name)
    {
        $name = (string)$name;
        $rowSet = $this->_tableGateway->select(array('name' => $name));
        return ($rowSet->current()) ? true : false;
    }

    public function add(Region $object)
    {
        $data = array(
            'name' => $object->name,
        );
        if ($this->issetName($object->name)) {
            throw new \Exception("$object->name in the table exists");
        } else {
            $this->_tableGateway->insert($data);
        }
    }

    public function update(Region $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->_tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function remove($id)
    {
        $this->_tableGateway->delete(array('id' => $id));
    }

}
