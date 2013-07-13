<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\FlightDataFilter;

class FlightDataModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightBaseDataForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new FlightDataFilter($this->adapter));
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
        $row->dateOfFlight = date('d/m/Y', $row->dateOfFlight);

        return $row;
    }

    /**
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getDataById($id)
    {
        $id = (string)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns(array('id',
            'parentFormId',
            'dateOfFlight',
            'flightNumberIcaoAndIata',
            'flightNumberText',
            'apDepIcaoAndIata',
            'apDepTime',
            'apArrIcaoAndIata',
            'apArrTime'));
        $select->join(array('library_air_operator' => 'library_air_operator'),
            'library_air_operator.id = flightBaseDataForm.flightNumberIcaoAndIata',
            array('flightNumberIcao' => 'code_icao', 'flightNumberIata' => 'code_iata'));
//        $select->join(array('library_air_operator' => 'library_air_operator'),
//            'library_air_operator.id = flightBaseHeaderForm.airOperator',
//            array('airOperatorShortName' => 'short_name'));
//        $select->join(array('library_aircraft' => 'library_aircraft'),
//            'library_aircraft.reg_number = flightBaseHeaderForm.aircraft',
//            array('aircraftType' => 'aircraft_type'));
        $select->where(array('parentFormId' => $id));
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
//        $temp = $resultSet->count();
//        \Zend\Debug\Debug::dump($resultSet);

        $data = array();
        foreach ($resultSet as $row) {
//            \Zend\Debug\Debug::dump($row);
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['parentFormId'] = $row->parentFormId;
            $data[$row->id]['dateOfFlight'] = date('d/m/Y', $row->dateOfFlight);
            $data[$row->id]['flightNumberIcaoAndIata'] = $row->flightNumberIcaoAndIata;
            $data[$row->id]['flightNumberText'] = $row->flightNumberText;
            $data[$row->id]['apDepIcaoAndIata'] = $row->apDepIcaoAndIata;
            $data[$row->id]['apDepTime'] = $row->apDepTime;
            $data[$row->id]['apArrIcaoAndIata'] = $row->apArrIcaoAndIata;
            $data[$row->id]['apArrTime'] = $row->apArrTime;
            $data[$row->id]['flightNumberIcao'] = $row->flightNumberIcao;
            $data[$row->id]['flightNumberIata'] = $row->flightNumberIata;

        }
//        \Zend\Debug\Debug::dump($data);

        return $data;
    }

    /**
     * @param FlightDataFilter $object
     * @return string
     */
    public function add(FlightDataFilter $object)
    {
        $dateOfFlight = \DateTime::createFromFormat('d-m-Y', $object->dateOfFlight);
        $apDepTime = \DateTime::createFromFormat('d-m-Y H:i', $object->dateOfFlight . ' ' . $object->apDepTime);
        $apArrTime = \DateTime::createFromFormat('d-m-Y H:i', $object->dateOfFlight . ' ' . $object->apArrTime);

        $data = array(
            'parentFormId' => $object->parentFormId,
            'dateOfFlight' => $dateOfFlight->getTimestamp(),
            'flightNumberIcaoAndIata' => $object->flightNumberIcaoAndIata,
            'flightNumberText' => $object->flightNumberText,
            'apDepIcaoAndIata' => $object->apDepIcaoAndIata,
            'apDepTime' => $apDepTime->getTimestamp(),
            'apArrIcaoAndIata' => $object->apArrIcaoAndIata,
            'apArrTime' => $apArrTime->getTimestamp(),
        );
        $hash = $object->dateOfFlight . ': Dep ' . $object->apDepTime . ', Arr ' . $object->apArrTime . '.';

        $this->insert($data);

        return $hash;
    }
}
