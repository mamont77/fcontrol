<?php

namespace FcLibraries\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class CountryTable extends AbstractTableGateway implements ModelInterface
{
    protected $table = 'library_country';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Country());

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
        $select->columns(array('id', 'name', 'code'));
        $select->join(array('r' => 'library_region'),
            'r.id = library_country.region',
            array('region_name' => 'name'));
//        $select->order('library_country.code ASC');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

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
        $data = array(
            'name' => $object->name,
            'region' => $object->region,
            'code' => $object->code,
        );
        if ($this->existName($object->name)) {
            throw new \Exception("$object->name in the table exists");
        } else {
            $this->insert($data);
        }
    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function save(Country $object)
    {
        $data = array(
            'name' => $object->name,
            'region' => $object->region,
            'code' => $object->code,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

}
