<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\TransferFilter;

/**
 * Class TransferModel
 * @package FcFlight\Model
 */
class TransferModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightTransferForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new TransferFilter($this->adapter));
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
        $select->columns(array('id',
            'headerId',
            'airportId',
            'isNeed',
            'agentId'
        ));

        $select->join(array('airport' => 'library_airport'),
            'flightTransferForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('agent' => 'library_kontragent'),
            'flightTransferForm.agentId = agent.id',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param TransferFilter $object
     * @return array
     */
    public function add(TransferFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'airportId' => (int)$object->airportId,
            'isNeed' => (int)$object->isNeed,
            'agentId' => (string)$object->agentId,
        );

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
        );
    }

    /**
     * @param TransferFilter $object
     * @throws \Exception
     */
    public function save(TransferFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'airportId' => (int)$object->airportId,
            'isNeed' => (int)$object->isNeed,
            'agentId' => (string)$object->agentId,
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
        $select->columns(array('id',
            'headerId',
            'airportId',
            'isNeed',
            'agentId'
        ));

        $select->join(array('airport' => 'library_airport'),
            'flightTransferForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->join(array('agent' => 'library_kontragent'),
            'flightTransferForm.agentId = agent.id',
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
            $data[$row->id]['airportId'] = $row->airportId;
            $data[$row->id]['isNeed'] = $row->isNeed;
            $data[$row->id]['agentId'] = $row->agentId;

            //Virtual fields
            $data[$row->id]['icao'] = $row->icao;
            $data[$row->id]['iata'] = $row->iata;
            $data[$row->id]['airportName'] = $row->airportName;
            $data[$row->id]['kontragentShortName'] = $row->kontragentShortName;
        }

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByTransferId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
