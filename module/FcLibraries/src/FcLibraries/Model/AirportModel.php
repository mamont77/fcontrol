<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\AirportFilter;

/**
 * Class AirportModel
 * @package FcLibraries\Model
 */
class AirportModel extends BaseModel
{
    protected $table = 'library_airport';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AirportFilter($this->adapter));

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
        $select->join(array('city' => 'library_city'),
            'library_airport.city_id = city.id',
            array('city_name' => 'name'), 'left');
        $select->join(array('country' => 'library_country'),
            'city.country_id = country.id',
            array('country_name' => 'name'), 'left');
        $select->join(array('region' => 'library_region'),
            'country.region_id = region.id',
            array('region_name' => 'name'), 'left');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param AirportFilter $object
     */
    public function add(AirportFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'city_id' => $object->city_id,
        );
        $this->insert($data);
    }

    /**
     * @param AirportFilter $object
     * @throws \Exception
     */
    public function save(AirportFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'city_id' => $object->city_id,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

}
