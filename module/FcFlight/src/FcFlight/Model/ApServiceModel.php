<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\ApServiceFilter;

/**
 * Class ApServiceModel
 * @package FcFlight\Model
 */
class ApServiceModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = 'flightApServiceForm';

    /**
     * @var array
     */
    public $_tableFields = array(
        'id',
        'headerId',
        'legId',
        'airportId',
        'typeOfApServiceId',
        'agentId',
        'price',
        'currency',
        'exchangeRate',
        'priceUSD',
    );

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new ApServiceFilter($this->adapter));
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
            'flightApServiceForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('typeOfApService' => 'library_type_of_ap_service'),
            'flightApServiceForm.typeOfApServiceId = typeOfApService.id',
            array('typeOfApServiceName' => 'name'), 'left');

        $select->join(array('agent' => 'library_kontragent'),
            'flightApServiceForm.agentId = agent.id',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->airportId = $row->legId . '-' . $row->airportId;

        return $row;
    }

    /**
     * @param ApServiceFilter $object
     * @return array
     */
    public function add(ApServiceFilter $object)
    {

        $airport = explode('-', (string)$object->airportId);
        $object->legId = $airport[0];
        $object->airportId = $airport[1];

        $data = array(
            'headerId' => (int)$object->headerId,
            'legId' => (int)$object->legId,
            'airportId' => (int)$object->airportId,
            'typeOfApServiceId' => (int)$object->typeOfApServiceId,
            'agentId' => (int)$object->agentId,
            'price' => (string)$object->price,
            'currency' => (string)$object->currency,
            'exchangeRate' => (string)$object->exchangeRate,
            'priceUSD' => (string)$object->priceUSD,
        );

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
        );
    }

    /**
     * @param ApServiceFilter $object
     * @throws \Exception
     */
    public function save(ApServiceFilter $object)
    {
        $airport = explode('-', (string)$object->airportId);
        $object->legId = $airport[0];
        $object->airportId = $airport[1];

        $data = array(
            'headerId' => (int)$object->headerId,
            'legId' => (int)$object->legId,
            'airportId' => (int)$object->airportId,
            'typeOfApServiceId' => (int)$object->typeOfApServiceId,
            'agentId' => (int)$object->agentId,
            'price' => (string)$object->price,
            'currency' => (string)$object->currency,
            'exchangeRate' => (string)$object->exchangeRate,
            'priceUSD' => (string)$object->priceUSD,
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
     * @param $id
     * @return array
     */
    public function getByHeaderId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->_tableFields);

        $select->join(array('airport' => 'library_airport'),
            'flightApServiceForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('typeOfApService' => 'library_type_of_ap_service'),
            'flightApServiceForm.typeOfApServiceId = typeOfApService.id',
            array('typeOfApServiceName' => 'name'), 'left');

        $select->join(array('agent' => 'library_kontragent'),
            'flightApServiceForm.agentId = agent.id',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->where(array('headerId' => $id));
        $select->order(array('id ' . $select::ORDER_ASCENDING, 'airportId ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
//            \Zend\Debug\Debug::dump($row);
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['legId'] = $row->legId;
            $data[$row->id]['airportId'] = $row->airportId;
            $data[$row->id]['agentId'] = $row->agentId;
            $data[$row->id]['price'] = $row->price;
            $data[$row->id]['currency'] = $row->currency;
            $data[$row->id]['exchangeRate'] = $row->exchangeRate;
            $data[$row->id]['priceUSD'] = $row->priceUSD;

            //Virtual fields
            $data[$row->id]['icao'] = $row->icao;
            $data[$row->id]['iata'] = $row->iata;
            $data[$row->id]['airportName'] = $row->airportName;
            $data[$row->id]['typeOfApServiceName'] = $row->typeOfApServiceName;
            $data[$row->id]['kontragentShortName'] = $row->kontragentShortName;
        }

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByApServiceId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
