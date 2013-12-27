<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\FlightHeaderFilter;

/**
 * Class FlightHeaderModel
 * @package FcFlight\Model
 */
class FlightHeaderModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = 'flightBaseHeaderForm';

    /**
     * @var array
     */
    protected $_tableFields = array(
        'id',
        'parentId',
        'refNumberOrder',
        'dateOrder',
        'kontragent',
        'airOperator',
        'aircraftId',
        'alternativeAircraftId1',
        'alternativeAircraftId2',
        'status'
    );

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
     * @param \Zend\Db\Sql\Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null, $status = 1)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns($this->_tableFields);

        $select->join(array('libraryKontragent' => 'library_kontragent'),
            'libraryKontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->join(array('libraryAirOperator' => 'library_air_operator'),
            'libraryAirOperator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');

        $select->join(array('libraryAircraft' => 'library_aircraft'),
            'libraryAircraft.id = flightBaseHeaderForm.aircraftId',
            array('aircraftTypeId' => 'aircraft_type', 'aircraftName' => 'reg_number'), 'left');

        $select->join(array('libraryAircraftType' => 'library_aircraft_type'),
            'libraryAircraftType.id = libraryAircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft1' => 'library_aircraft'),
            'libraryAlternativeAircraft1.id = flightBaseHeaderForm.alternativeAircraftId1',
            array('alternativeAircraftTypeId1' => 'aircraft_type', 'alternativeAircraftName1' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft1' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft1.id = libraryAlternativeAircraft1.aircraft_type',
            array('alternativeAircraftTypeName1' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft2' => 'library_aircraft'),
            'libraryAlternativeAircraft2.id = flightBaseHeaderForm.alternativeAircraftId2',
            array('alternativeAircraftTypeId2' => 'aircraft_type', 'alternativeAircraftName2' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft2' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft2.id = libraryAlternativeAircraft2.aircraft_type',
            array('alternativeAircraftTypeName2' => 'name'), 'left');

        $select->where(array('status' => $status));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
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

        $select->join(array('libraryKontragent' => 'library_kontragent'),
            'libraryKontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->join(array('libraryAirOperator' => 'library_air_operator'),
            'libraryAirOperator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');

        $select->join(array('libraryAircraft' => 'library_aircraft'),
            'libraryAircraft.id = flightBaseHeaderForm.aircraftId',
            array('aircraftTypeId' => 'aircraft_type', 'aircraftName' => 'reg_number'), 'left');

        $select->join(array('libraryAircraftType' => 'library_aircraft_type'),
            'libraryAircraftType.id = libraryAircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft1' => 'library_aircraft'),
            'libraryAlternativeAircraft1.id = flightBaseHeaderForm.alternativeAircraftId1',
            array('alternativeAircraftTypeId1' => 'aircraft_type', 'alternativeAircraftName1' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft1' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft1.id = libraryAlternativeAircraft1.aircraft_type',
            array('alternativeAircraftTypeName1' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft2' => 'library_aircraft'),
            'libraryAlternativeAircraft2.id = flightBaseHeaderForm.alternativeAircraftId2',
            array('alternativeAircraftTypeId2' => 'aircraft_type', 'alternativeAircraftName2' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft2' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft2.id = libraryAlternativeAircraft2.aircraft_type',
            array('alternativeAircraftTypeName2' => 'name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->dateOrder = date('d-m-Y', $row->dateOrder);

        return $row;
    }

    /**
     * @param FlightHeaderFilter $object
     * @return array
     */
    public function add(FlightHeaderFilter $object)
    {
        $dateOrder = \DateTime::createFromFormat('d-m-Y', $object->dateOrder);
        $dateOrder = $dateOrder->setTime(0, 0)->getTimestamp();

        if (empty($object->id)) {
            $object->id = null;
        }

        $data = array(
            'parentId' => $object->id,
            'refNumberOrder' => $this->getLastRefNumberOrder($dateOrder),
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraftId' => $object->aircraftId,
            'alternativeAircraftId1' => $object->alternativeAircraftId1,
            'alternativeAircraftId2' => $object->alternativeAircraftId2,
            'status' => $object->status,
        );


        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
            'refNumberOrder' => $data['refNumberOrder'],
        );
    }

    /**
     * @param \FcFlight\Filter\FlightHeaderFilter $object
     * @return mixed
     * @throws \Exception
     */
    public function save(FlightHeaderFilter $object)
    {
        $dateOrder = \DateTime::createFromFormat('d-m-Y', $object->dateOrder);
        $dateOrder = $dateOrder->setTime(0, 0)->getTimestamp();
        
        $data = array(
            'refNumberOrder' => $object->refNumberOrder,
            'dateOrder' => $dateOrder,
            'kontragent' => $object->kontragent,
            'airOperator' => $object->airOperator,
            'aircraftId' => $object->aircraftId,
            'alternativeAircraftId1' => $object->alternativeAircraftId1,
            'alternativeAircraftId2' => $object->alternativeAircraftId2,
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
        $select->columns($this->_tableFields);

        $select->join(array('libraryKontragent' => 'library_kontragent'),
            'libraryKontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->join(array('libraryAirOperator' => 'library_air_operator'),
            'libraryAirOperator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');

        $select->join(array('libraryAircraft' => 'library_aircraft'),
            'libraryAircraft.id = flightBaseHeaderForm.aircraftId',
            array('aircraftTypeId' => 'aircraft_type', 'aircraftName' => 'reg_number'), 'left');

        $select->join(array('libraryAircraftType' => 'library_aircraft_type'),
            'libraryAircraftType.id = libraryAircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft1' => 'library_aircraft'),
            'libraryAlternativeAircraft1.id = flightBaseHeaderForm.alternativeAircraftId1',
            array('alternativeAircraftTypeId1' => 'aircraft_type', 'alternativeAircraftName1' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft1' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft1.id = libraryAlternativeAircraft1.aircraft_type',
            array('alternativeAircraftTypeName1' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft2' => 'library_aircraft'),
            'libraryAlternativeAircraft2.id = flightBaseHeaderForm.alternativeAircraftId2',
            array('alternativeAircraftTypeId2' => 'aircraft_type', 'alternativeAircraftName2' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft2' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft2.id = libraryAlternativeAircraft2.aircraft_type',
            array('alternativeAircraftTypeName2' => 'name'), 'left');

        $select->where(array('refNumberOrder' => $refNumberOrder));
        $row = $this->selectWith($select)->current();

        if (!$row) {
            throw new \Exception("Could not find row $refNumberOrder");
        }

        $row->dateOrder = date('d-m-Y', $row->dateOrder);

        return $row;
    }

    /**
     * Get Reg Number Order from Main data
     *
     * @param $id
     * @return mixed
     */
    public function getRefNumberOrderById($id)
    {
        $row = $this->get($id);
        return $row->refNumberOrder;
    }

    /**
     * Get status from Main data
     * @param $id
     * @return mixed
     */
    public function getStatusById($id)
    {
        $row = $this->get($id);
        return $row->status;
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
