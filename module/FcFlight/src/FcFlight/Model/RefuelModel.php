<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\RefuelFilter;

/**
 * Class RefuelModel
 * @package FcFlight\Model
 */
class RefuelModel extends AbstractTableGateway
{

    /**
     * @var string
     */

    public $table = 'flightRefuelForm';

    /**
     * @var array
     */
    protected $_tableFields = array(
        'id',
        'headerId',
        'agentId',
        'legId',
        'quantityLtr',
        'quantityOtherUnits',
        'unitId',
        'priceUsd',
        'totalPriceUsd',
        'date',
    );

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
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->_tableFields);

        $select->join(array('agent' => 'library_kontragent'),
            'flightRefuelForm.agentId = agent.id',
            array('agentName' => 'name', 'agentAddress' => 'address', 'agentMail' => 'mail'), 'left');

        $select->join(array('leg' => 'flightLegForm'),
            'flightRefuelForm.legId = leg.id',
            array('airportDepartureId' => 'apDepAirportId', 'airportArrivalId' => 'apArrAirportId'), 'left');

        $select->join(array('airportDeparture' => 'library_airport'),
            'leg.apDepAirportId = airportDeparture.id',
            array('airportDepartureICAO' => 'code_icao', 'airportDepartureIATA' => 'code_iata'), 'left');

        $select->join(array('airportArrival' => 'library_airport'),
            'leg.apArrAirportId = airportArrival.id',
            array('airportArrivalICAO' => 'code_icao', 'airportArrivalIATA' => 'code_iata'), 'left');

        $select->join(array('unit' => 'library_unit'),
            'flightRefuelForm.unitId = unit.id',
            array('unitName' => 'name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
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

        $select->columns($this->_tableFields);

        $select->join(array('agent' => 'library_kontragent'),
            'flightRefuelForm.agentId = agent.id',
            array('agentName' => 'name', 'agentAddress' => 'address', 'agentMail' => 'mail'), 'left');

        $select->join(array('leg' => 'flightLegForm'),
            'flightRefuelForm.legId = leg.id',
            array('airportDepartureId' => 'apDepAirportId', 'airportArrivalId' => 'apArrAirportId'), 'left');

        $select->join(array('airportDeparture' => 'library_airport'),
            'leg.apDepAirportId = airportDeparture.id',
            array('airportDepartureICAO' => 'code_icao', 'airportDepartureIATA' => 'code_iata'), 'left');

        $select->join(array('airportArrival' => 'library_airport'),
            'leg.apArrAirportId = airportArrival.id',
            array('airportArrivalICAO' => 'code_icao', 'airportArrivalIATA' => 'code_iata'), 'left');

        $select->join(array('unit' => 'library_unit'),
            'flightRefuelForm.unitId = unit.id',
            array('unitName' => 'name'), 'left');

        $select->where(array($this->table . '.headerId' => $id));
        $select->order('date ' . $select::ORDER_ASCENDING);

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
            $data[$row->legId] = array(
                'headerId' => $row->headerId,
                'legName' => $row->airportDepartureICAO . ' (' . $row->airportDepartureIATA . ')'
                    . ' ⇒ '
                    . $row->airportArrivalICAO . ' (' . $row->airportArrivalIATA . ')',
            );
        }
        foreach ($resultSet as $row) {
            $data[$row->legId]['refuel'][$row->id] = array(
                'agentId' => $row->agentId,
                'agentName' => $row->agentName,
                'agentAddress' => $row->agentAddress,
                'agentMail' => $row->agentMail,
                'quantityLtr' => $row->quantityLtr,
                'quantityOtherUnits' => $row->quantityOtherUnits,
                'unitId' => $row->unitId,
                'unitName' => $row->unitName,
                'priceUsd' => $row->priceUsd,
                'totalPriceUsd' => $row->totalPriceUsd,
                'date' => date('d-m-Y', $row->date),
            );
        }


        return $data;
    }

    /**
     * @param RefuelFilter $object
     * @return array
     */
    public function add(RefuelFilter $object)
    {
        $date = \DateTime::createFromFormat('d-m-Y', $object->date);

        $data = array(
            'headerId' => (int)$object->headerId,
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'quantityLtr' => (float)$object->quantityLtr,
            'quantityOtherUnits' => (float)$object->quantityOtherUnits,
            'unitId' => (int)$object->unitId,
            'priceUsd' => (float)$object->priceUsd,
            'totalPriceUsd' => (float)$object->totalPriceUsd,
            'date' => (string)$date->getTimestamp(),
        );
        $hash = $object->date;

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
            'hash' => $hash,
        );
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
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'quantityLtr' => (float)$object->quantityLtr,
            'quantityOtherUnits' => (float)$object->quantityOtherUnits,
            'unitId' => (int)$object->unitId,
            'priceUsd' => (float)$object->priceUsd,
            'totalPriceUsd' => (float)$object->totalPriceUsd,
            'date' => (string)$date->getTimestamp(),
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

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByRefuelId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
