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
            'headerId',
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
        $select->join(array('dep' => 'library_airport'),
            'dep.id = flightBaseDataForm.apDepIcaoAndIata',
            array('apDepIcao' => 'code_icao', 'apDepIata' => 'code_iata'));
        $select->join(array('arr' => 'library_airport'),
            'arr.id = flightBaseDataForm.apArrIcaoAndIata',
            array('apArrIcao' => 'code_icao', 'apArrIata' => 'code_iata'));
        $select->where(array('headerId' => $id));
        $select->order('dateOfFlight ' . $select::ORDER_ASCENDING);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['dateOfFlight'] = date('d/m/Y', $row->dateOfFlight);
            $data[$row->id]['flightNumberIcaoAndIata'] = $row->flightNumberIcaoAndIata;
            $data[$row->id]['flightNumberText'] = $row->flightNumberText;
            $data[$row->id]['apDepIcaoAndIata'] = $row->apDepIcaoAndIata;
            $data[$row->id]['apDepTime'] = date('H:i', $row->apDepTime);
            $data[$row->id]['apArrIcaoAndIata'] = $row->apArrIcaoAndIata;
            $data[$row->id]['apArrTime'] = date('H:i', $row->apArrTime);
            //Virtual fields from join
            $data[$row->id]['flightNumberIcao'] = $row->flightNumberIcao;
            $data[$row->id]['flightNumberIata'] = $row->flightNumberIata;
            $data[$row->id]['apDepIcao'] = $row->apDepIcao;
            $data[$row->id]['apDepIata'] = $row->apDepIata;
            $data[$row->id]['apArrIcao'] = $row->apArrIcao;
            $data[$row->id]['apArrIata'] = $row->apArrIata;
        }

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
            'headerId' => (int)$object->headerId,
            'dateOfFlight' => (string)$dateOfFlight->getTimestamp(),
            'flightNumberIcaoAndIata' => (int)$object->flightNumberIcaoAndIata,
            'flightNumberText' => (string)$object->flightNumberText,
            'apDepIcaoAndIata' => (int)$object->apDepIcaoAndIata,
            'apDepTime' => (string)$apDepTime->getTimestamp(),
            'apArrIcaoAndIata' => (int)$object->apArrIcaoAndIata,
            'apArrTime' => (string)$apArrTime->getTimestamp(),
        );
        $hash = $object->dateOfFlight . ': Dep ' . $object->apDepTime . ', Arr ' . $object->apArrTime . '.';

        $this->insert($data);

        return $hash;
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->delete(array('id' => $id));
    }

    public function getHeaderRefNumberOrderByDataId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
