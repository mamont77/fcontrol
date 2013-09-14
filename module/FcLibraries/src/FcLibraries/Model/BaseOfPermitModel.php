<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\BaseOfPermitFilter;

/**
 * Class BaseOfPermitModel
 * @package FcLibraries\Model
 */
class BaseOfPermitModel extends BaseModel
{
    protected $table = 'library_base_of_permit';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new BaseOfPermitFilter($this->adapter));

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
        $select->columns(array('id', 'airportId', 'termValidity', 'termToTake', 'infoToTake'));
        $select->join(array('airport' => 'library_airport'),
            'library_base_of_permit.airportId = airport.id',
            array('airportName' => 'name', 'cityId' => 'city_id'), 'left');

//        $select->join(array('city' => 'library_city'),
//            'library_airport.city_id = city.id',
//            array('cityName' => 'name'), 'left');


//            'airport.city_id = country.id',
//            array('country_name' => 'name'), 'left');
//        $select->join(array('region' => 'library_region'),
//            'country.region_id = region.id',
//            array('region_name' => 'name'), 'left');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param BaseOfPermitFilter $object
     */
    public function add(BaseOfPermitFilter $object)
    {
        $data = array(
            'airportId' => $object->airportId,
            'termValidity' => $object->termValidity,
            'termToTake' => $object->termToTake,
            'infoToTake' => $object->infoToTake,
        );
        $this->insert($data);
    }

    /**
     * @param BaseOfPermitFilter $object
     * @throws \Exception
     */
    public function save(BaseOfPermitFilter $object)
    {
        $data = array(
            'airportId' => $object->airportId,
            'termValidity' => $object->termValidity,
            'termToTake' => $object->termToTake,
            'infoToTake' => $object->infoToTake,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
