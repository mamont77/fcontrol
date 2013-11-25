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
        'airportId',
        'quantityLtr',
        'quantityOtherUnits',
        'unitId',
        'priceUsd',
        'totalPriceUsd',
        'date',
        'status',
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

        $select->join(array('airport' => 'library_airport'),
            'flightRefuelForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');


        $select->join(array('agent' => 'library_kontragent'),
            'flightRefuelForm.agentId = agent.id',
            array('kontragentShortName' => 'short_name'), 'left');

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
        $row->airportId = $row->legId . '-' . $row->airportId;

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

        $select->join(array('airport' => 'library_airport'),
            'flightRefuelForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('agent' => 'library_kontragent'),
            'flightRefuelForm.agentId = agent.id',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->join(array('unit' => 'library_unit'),
            'flightRefuelForm.unitId = unit.id',
            array('unitName' => 'name'), 'left');

        $select->where(array($this->table . '.headerId' => $id));
        $select->order('date ' . $select::ORDER_ASCENDING);

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['legId'] = $row->legId;
            $data[$row->id]['airportId'] = $row->airportId;
            $data[$row->id]['agentId'] = $row->agentId;
            $data[$row->id]['quantityLtr'] = $row->quantityLtr;
            $data[$row->id]['quantityOtherUnits'] = $row->quantityOtherUnits;
            $data[$row->id]['unitId'] = $row->unitId;
            $data[$row->id]['priceUsd'] = $row->priceUsd;
            $data[$row->id]['totalPriceUsd'] = $row->totalPriceUsd;
            $data[$row->id]['date'] = $row->date;
            $data[$row->id]['status'] = $row->status;

            //Virtual fields
            $data[$row->id]['icao'] = $row->icao;
            $data[$row->id]['iata'] = $row->iata;
            $data[$row->id]['airportName'] = $row->airportName;
            $data[$row->id]['kontragentShortName'] = $row->kontragentShortName;
            $data[$row->id]['unitName'] = $row->unitName;

        }
//        \Zend\Debug\Debug::dump($data);

        return $data;
    }

    /**
     * @param RefuelFilter $object
     * @return array
     */
    public function add(RefuelFilter $object)
    {
        $date = \DateTime::createFromFormat('d-m-Y', $object->date);
        $airport = explode('-', (string)$object->airportId);
        $object->legId = $airport[0];
        $object->airportId = $airport[1];

        $data = array(
            'headerId' => (int)$object->headerId,
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'airportId' => (int)$object->airportId,
            'quantityLtr' => (string)$object->quantityLtr,
            'quantityOtherUnits' => (string)$object->quantityOtherUnits,
            'unitId' => (int)$object->unitId,
            'priceUsd' => (string)$object->priceUsd,
            'totalPriceUsd' => (string)$object->totalPriceUsd,
            'date' => (string)$date->getTimestamp(),
            'status' => (int)$object->status,
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
        $airport = explode('-', (string)$object->airportId);
        $object->legId = $airport[0];
        $object->airportId = $airport[1];

        $data = array(
            'headerId' => (int)$object->headerId,
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'airportId' => (int)$object->airportId,
            'quantityLtr' => (string)$object->quantityLtr,
            'quantityOtherUnits' => (string)$object->quantityOtherUnits,
            'unitId' => (int)$object->unitId,
            'priceUsd' => (string)$object->priceUsd,
            'totalPriceUsd' => (string)$object->totalPriceUsd,
            'date' => (string)$date->getTimestamp(),
            'status' => (int)$object->status,
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
