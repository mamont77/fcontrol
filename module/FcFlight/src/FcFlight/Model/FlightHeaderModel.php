<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\FlightHeaderFilter;

class FlightHeaderModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightBaseHeaderForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new FlightHeaderFilter($this->adapter));
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
        $rowSet = $this->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        $row->dateOrder = date('Y-m-d', $row->dateOrder);

        return $row;
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null, $status = 1)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('id', 'refNumberOrder', 'dateOrder', 'kontragent', 'airOperator', 'aircraft', 'status'));
        $select->join(array('library_kontragent' => 'library_kontragent'),
            'library_kontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');
        $select->join(array('library_air_operator' => 'library_air_operator'),
            'library_air_operator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');
        $select->join(array('library_aircraft' => 'library_aircraft'),
            'library_aircraft.reg_number = flightBaseHeaderForm.aircraft',
            array('aircraftType' => 'aircraft_type'), 'left');
        $select->join(array('library_aircraft_type' => 'library_aircraft_type'),
            'library_aircraft_type.id = library_aircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');
        $select->where(array('status' => $status));
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param \FcFlight\Filter\FlightHeaderFilter $object
     * @return mixed
     */
    public function add(FlightHeaderFilter $object)
    {
        $dateOrder = \DateTime::createFromFormat('d-m-Y', $object->dateOrder);
        $dateOrder = $dateOrder->getTimestamp();

        $data = array(
            'refNumberOrder' => $this->getLastRefNumberOrder($dateOrder),
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraft' => $object->aircraft,
            'status' => $object->status,
        );
        $this->insert($data);

        return $data['refNumberOrder'];
    }

    /**
     * @param \FcFlight\Filter\FlightHeaderFilter $object
     * @return mixed
     * @throws \Exception
     */
    public function save(FlightHeaderFilter $object)
    {
        $dateOrder = strtotime($object->dateOrder);

        $data = array(
            'refNumberOrder' => $object->refNumberOrder,
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraft' => $object->aircraft,
            'status' => $object->status,
        );
        $id = (int)$object->id;
        $oldData = $this->get($id);
        if ($oldData) {
            if ($oldData->dateOrder != date('d-m-Y', $data['dateOrder'])) {
                $data['refNumberOrder'] = $this->getLastRefNumberOrder($dateOrder);
            }
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }

        return $data['refNumberOrder'];
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

    /**
     * @param $refNumberOrder
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByRefNumberOrder($refNumberOrder)
    {
        $refNumberOrder = (string)$refNumberOrder;

        $select = $this->getSql()->select();
        $select->columns(array('id', 'refNumberOrder', 'dateOrder', 'kontragent', 'airOperator', 'aircraft', 'status'));
        $select->join(array('library_kontragent' => 'library_kontragent'),
            'library_kontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');
        $select->join(array('library_air_operator' => 'library_air_operator'),
            'library_air_operator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');
        $select->join(array('library_aircraft' => 'library_aircraft'),
            'library_aircraft.reg_number = flightBaseHeaderForm.aircraft',
            array('aircraftType' => 'aircraft_type'), 'left');
        $select->join(array('library_aircraft_type' => 'library_aircraft_type'),
            'library_aircraft_type.id = library_aircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');
        $select->where(array('refNumberOrder' => $refNumberOrder));
        $row = $this->selectWith($select)->current();
        if (!$row) {
            throw new \Exception("Could not find row $refNumberOrder");
        }

        return $row;
    }

    public function getRefNumberOrderById($id)
    {
        $row = $this->get($id);
        return $row->refNumberOrder;
    }

    /**
     * @param int $dateOrder
     * @return string
     */
    public function getLastRefNumberOrder($dateOrder)
    {
        /*
        * ORD-YYMMDD-1
        */
        $refNumberOrder = 'ORD-' . date('ymd', $dateOrder) . '-';
        $allSimilarNumbers = array();
        $result = $this->_findSimilarRefNumberOrder($refNumberOrder);

        if ($result) {
            foreach ($result as $row) {
                $parsedNumber = explode('-', $row->refNumberOrder);
                $allSimilarNumbers[$parsedNumber[2]] = $parsedNumber[1];
            }
            krsort($allSimilarNumbers);
            $lastKey = array_slice($allSimilarNumbers, 0, 1, true);
            $lastKey = key($lastKey);
            $nextKey = (int)$lastKey + 1;
            $refNumberOrder = $refNumberOrder . $nextKey;
        } else {
            $refNumberOrder = $refNumberOrder . '1';
        }

        return $refNumberOrder;
    }

    /**
     * @param $refNumberOrder
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    private function _findSimilarRefNumberOrder($refNumberOrder)
    {
        $refNumberOrder = (string)$refNumberOrder;
        $select = new Select();
        $select->from($this->table);
        $select->columns(array('refNumberOrder'));
        $select->where->like('refNumberOrder', $refNumberOrder . '%');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
