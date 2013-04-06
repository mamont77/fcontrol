<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\AircraftFilter;

class AircraftModel extends BaseModel
{
    protected $table = 'library_aircraft';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AircraftFilter($this->adapter));

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
        $select->columns(array('id', 'aircraft_type', 'reg_number'));
        $select->join(array('t' => 'library_aircraft_type'),
            't.id = library_aircraft.aircraft_type',
            array('aircraft_type_name' => 'name'));
//        $select->order('library_country.code ASC');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param \FcLibraries\Filter\AircraftFilter $object
     */
    public function add(AircraftFilter $object)
    {
        $data = array(
            'aircraft_type' => $object->aircraft_type,
            'reg_number' => $object->reg_number,
        );
        $this->insert($data);
    }

    /**
     * @param \FcLibraries\Filter\AircraftFilter $object
     * @throws \Exception
     */
    public function save(AircraftFilter $object)
    {
        $data = array(
            'aircraft_type' => $object->aircraft_type,
            'reg_number' => $object->reg_number,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

}