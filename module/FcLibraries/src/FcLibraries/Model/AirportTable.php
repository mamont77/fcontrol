<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\LibraryTable;

class AirportTable extends LibraryTable
{
    protected $table = 'library_airport';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Airport());

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
        $select->columns(array('id', 'name', 'short_name', 'code_icao', 'code_iata'));
        $select->join(array('c' => 'library_country'),
            'c.id = library_airport.country',
            array('country_name' => 'name'));
//        $select->order('library_country.code ASC');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param Country $object
     */
    public function add(Airport $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'country' => $object->country,
        );
        $this->insert($data);
    }

    /**
     * @param Country $object
     * @throws \Exception
     */
    public function save(Airport $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'country' => $object->country,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

}
