<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\LibraryTable;

class CountryTable extends LibraryTable
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
     * @param Country $object
     */
    public function add(Country $object)
    {
        $data = array(
            'name' => $object->name,
            'region' => $object->region,
            'code' => $object->code,
        );
        $this->insert($data);
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

}
