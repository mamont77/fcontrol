<?php

namespace FcLibraries\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class CountryTable implements ModelInterface
{
    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $_tableGateway;

    /**
     * @param \Zend\Db\TableGateway\TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->_tableGateway->select();
        return $resultSet;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
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

    /**
     * @param $name
     * @return bool
     */
    public function existName($name)
    {
        $name = (string)$name;
        $rowSet = $this->_tableGateway->select(array('name' => $name));
        return ($rowSet->current()) ? true : false;
    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function add(Country $object)
    {
        $data = array(
            'name' => $object->name,
            'region' => $object->region,
            'code' => $object->code,
        );
        if ($this->existName($object->name)) {
            throw new \Exception("$object->name in the table exists");
        } else {
            $this->_tableGateway->insert($data);
        }
    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function update(Country $object)
    {
        $data = array(
            'name' => $object->name,
            'region' => $object->region,
            'code' => $object->code,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->_tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->_tableGateway->delete(array('id' => $id));
    }

}
