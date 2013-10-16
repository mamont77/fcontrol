<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\PermissionFilter;

class PermissionModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightPermissionForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new PermissionFilter($this->adapter));
        $this->initialize();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function get($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param PermissionFilter $object
     * @return array
     */
    public function add(PermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'airportId' => (int)$object->airportId,
            'isNeed' => (int)$object->isNeed,
            'typeOfPermit' => (string)$object->typeOfPermit,
            'baseOfPermitId' => (int)$object->baseOfPermitId,
            'check' => (string)$object->check,
        );

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
        );
    }

    /**
     * @param PermissionFilter $object
     * @return string
     * @throws \Exception
     */
    public function save(PermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
        );
        $hash = '';

        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }

        return $hash;
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

    /**
     * @param $id
     * @return array
     */
    public function getByHeaderId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns(array('id',
            'headerId',
            'airportId',
            'isNeed',
            'typeOfPermit',
            'baseOfPermitId',
            'check'));

        $select->join(array('airport' => 'library_airport'),
            'airport.id = flightPermissionForm.airportId',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('base_of_permit' => 'library_base_of_permit'),
            'base_of_permit.id = flightPermissionForm.baseOfPermitId',
            array('baseOfPermitAirportId' => 'airportId', 'termValidity' => 'termValidity', 'termToTake' => 'termToTake'), 'left');

        $select->where(array('headerId' => $id));
        $select->order(array('airportId ' . $select::ORDER_ASCENDING, 'id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
//            \Zend\Debug\Debug::dump($row);
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['airportId'] = $row->airportId;
            $data[$row->id]['isNeed'] = $row->isNeed;
            $data[$row->id]['typeOfPermit'] = $row->typeOfPermit;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['baseOfPermitId'] = $row->baseOfPermitId;
            $data[$row->id]['check'] = $row->check;
            $data[$row->id]['icao'] = $row->icao;
            $data[$row->id]['iata'] = $row->iata;
            $data[$row->id]['airportName'] = $row->airportName;
            $data[$row->id]['baseOfPermitAirportId'] = $row->baseOfPermitAirportId;
            $data[$row->id]['termValidity'] = $row->termValidity;
            $data[$row->id]['termToTake'] = $row->termToTake;
        }

        return $data;
    }

}
