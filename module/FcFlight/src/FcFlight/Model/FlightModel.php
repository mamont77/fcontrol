<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\FlightFilter;

class FlightModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightBaseForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new FlightFilter($this->adapter));
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
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('id', 'refNumberOrder', 'dateOrder', 'kontragent', 'airOperator', 'aircraft'));
        $select->join(array('library_kontragent' => 'library_kontragent'),
            'library_kontragent.id = flightBaseForm.kontragent',
            array('kontragentShortName' => 'short_name'));
        $select->join(array('library_air_operator' => 'library_air_operator'),
            'library_air_operator.id = flightBaseForm.airOperator',
            array('airOperatorShortName' => 'short_name'));
        $select->join(array('library_aircraft' => 'library_aircraft'),
            'library_aircraft.reg_number = flightBaseForm.aircraft',
            array('aircraftType' => 'aircraft_type'));
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param \FcFlight\Filter\FlightFilter $object
     */
    public function add(FlightFilter $object)
    {
        /*
         * $dateOrder = '1977-03-10';//YYYY-MM-DD
         * $dateOrder = '2014-03-10';//YYYY-MM-DD
         */
        $dateOrder = strtotime($object->dateOrder);

        $data = array(
            'refNumberOrder' => 'ORD-' . date('ymd', $dateOrder) . '/' . date('s', $dateOrder), //TODO ORD-YYMMDD/1
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraft' => $object->aircraft,
        );
        $this->insert($data);

        return $data['refNumberOrder'];
    }

    /**
     * @param \FcFlight\Filter\FlightFilter $object
     * @throws \Exception
     */
    public function save(FlightFilter $object)
    {
        $dateOrder = strtotime($object->dateOrder);

        $data = array(
            'refNumberOrder' => 'ORD-' . date('ymd', $dateOrder) . '/' . date('s', $dateOrder), //TODO ORD-YYMMDD/1
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraft' => $object->aircraft,
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

    /**
     * @param $refNumber
     * @return array|\ArrayObject|bool|null
     */
    private function _findSimilarRefNumberOrder($refNumber)
    {
        $refNumber = (string)$refNumber;
        $rowSet = $this->select(array('refNumberOrder' => $refNumber));
        $this->select->limit(5);
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }

        return $row;


    }


}
