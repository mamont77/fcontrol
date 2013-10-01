<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\AircraftTypeFilter;

/**
 * Class AircraftTypeModel
 * @package FcLibraries\Model
 */
class AircraftTypeModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_aircraft_type';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AircraftTypeFilter($this->adapter));
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

    /**
     * @param AircraftTypeFilter $object
     * @return int
     */
    public function add(AircraftTypeFilter $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param AircraftTypeFilter $object
     * @throws \Exception
     */
    public function save(AircraftTypeFilter $object)
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
}
