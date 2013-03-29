<?php

namespace FcLibraries\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class LibraryTable extends AbstractTableGateway implements ModelInterface
{
    protected $table = null;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null)
    {

    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
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

    /**
     * @param $name
     * @return bool
     */
    public function existName($name)
    {
        $name = (string)$name;
        $rowSet = $this->select(array('name' => $name));

        return ($rowSet->current()) ? true : false;
    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function add(Country $object)
    {

    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function save(Country $object)
    {

    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

}
