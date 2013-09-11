<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\RefuelFilter;

class RefuelModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightRefuelForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new RefuelFilter($this->adapter));
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
        $row->date = date('d-m-Y', $row->date);

        return $row;
    }

    /**
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByHeaderId($id)
    {
        $id = (string)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns(array('id',
            'headerId',
            'airport',
            'date',
            'agent',
            'quantity',
            'unit'));
        $select->join(array('library_airport' => 'library_airport'),
            'library_airport.id = flightRefuelForm.airport',
            array('airportName' => 'name', 'airportIcao' => 'code_icao', 'airportIata' => 'code_iata'), 'left');
        $select->join(array('library_kontragent' => 'library_kontragent'),
            'library_kontragent.id = flightRefuelForm.agent',
            array('agentName' => 'name'), 'left');
        $select->join(array('library_unit' => 'library_unit'),
            'library_unit.id = flightRefuelForm.unit',
            array('unitName' => 'name'), 'left');
        $select->where(array('headerId' => $id));
        $select->order('date ' . $select::ORDER_ASCENDING);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['airport'] = $row->airport;
            $data[$row->id]['date'] = date('d-m-Y', $row->date);
            $data[$row->id]['agent'] = $row->agent;
            $data[$row->id]['quantity'] = $row->quantity;
            $data[$row->id]['unit'] = $row->unit;
            //Virtual fields from join
            $data[$row->id]['airportName'] = $row->airportName;
            $data[$row->id]['airportIcao'] = $row->airportIcao;
            $data[$row->id]['airportIata'] = $row->airportIata;
            $data[$row->id]['agentName'] = $row->agentName;
            $data[$row->id]['unitName'] = $row->unitName;
        }

        return $data;
    }

    /**
     * @param RefuelFilter $object
     * @return string
     */
    public function add(RefuelFilter $object)
    {
        $date = \DateTime::createFromFormat('d-m-Y', $object->date);

        $data = array(
            'headerId' => (int)$object->headerId,
            'airport' => (int)$object->airport,
            'date' => (string)$date->getTimestamp(),
            'agent' => (int)$object->agent,
            'quantity' => (string)$object->quantity,
            'unit' => (int)$object->unit,
        );
        $hash = $object->date;

        $this->insert($data);

        return $hash;
    }

    /**
     * @param RefuelFilter $object
     * @return mixed
     * @throws \Exception
     */
    public function save(RefuelFilter $object)
    {
        $date = \DateTime::createFromFormat('d-m-Y', $object->date);

        $data = array(
            'headerId' => (int)$object->headerId,
            'airport' => (int)$object->airport,
            'date' => (string)$date->getTimestamp(),
            'agent' => (int)$object->agent,
            'quantity' => (string)$object->quantity,
            'unit' => (int)$object->unit,
        );
        $hash = $object->date;

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

    public function getHeaderRefNumberOrderByRefuelId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
