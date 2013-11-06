<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\PermissionFilter;

/**
 * Class PermissionModel
 * @package FcFlight\Model
 */
class PermissionModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightPermissionForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new PermissionFilter($this->adapter));
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

        $select->columns(array(
            'id',
            'headerId',
            'agentId',
            'legId',
            'countryId',
            'typeOfPermission',
            'permission',
            'comment',
        ));

        $select->join(array('agent' => 'library_kontragent'),
            'flightPermissionForm.agentId = agent.id',
            array('agentName' => 'name', 'agentAddress' => 'address', 'agentMail' => 'mail'), 'left');

        $select->join(array('leg' => 'flightLegForm'),
            'flightPermissionForm.legId = leg.id',
            array('airportDepartureId' => 'apDepAirportId', 'airportArrivalId' => 'apArrAirportId'), 'left');

        $select->join(array('airportDeparture' => 'library_airport'),
            'leg.apDepAirportId = airportDeparture.id',
            array('airportDepartureICAO' => 'code_icao', 'airportDepartureIATA' => 'code_iata'), 'left');

        $select->join(array('airportArrival' => 'library_airport'),
            'leg.apArrAirportId = airportArrival.id',
            array('airportArrivalICAO' => 'code_icao', 'airportArrivalIATA' => 'code_iata'), 'left');

        $select->join(array('country' => 'library_country'),
            'flightPermissionForm.countryId = country.id',
            array('countryName' => 'name', 'countryCode' => 'code'), 'left');

        $select->where(array('flightPermissionForm.id' => $id));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param PermissionFilter $object
     * @return array
     */
    public function add(PermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'countryId' => (int)$object->countryId,
            'typeOfPermission' => (string)$object->typeOfPermission,
            'permission' => (string)$object->permission,
            'comment' => (string)$object->comment,
        );

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
        );
    }

    /**
     * @param PermissionFilter $object
     * @throws \Exception
     */
    public function save(PermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'agentId' => (int)$object->agentId,
            'legId' => (int)$object->legId,
            'countryId' => (int)$object->countryId,
            'typeOfPermission' => (string)$object->typeOfPermission,
            'permission' => (string)$object->permission,
            'comment' => (string)$object->comment,
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

        $select->columns(array(
            'id',
            'headerId',
            'agentId',
            'legId',
            'countryId',
            'typeOfPermission',
            'permission',
            'comment',
        ));

        $select->join(array('agent' => 'library_kontragent'),
            'flightPermissionForm.agentId = agent.id',
            array('agentName' => 'name', 'agentAddress' => 'address', 'agentMail' => 'mail'), 'left');

        $select->join(array('leg' => 'flightLegForm'),
            'flightPermissionForm.legId = leg.id',
            array('airportDepartureId' => 'apDepAirportId', 'airportArrivalId' => 'apArrAirportId'), 'left');

        $select->join(array('airportDeparture' => 'library_airport'),
            'leg.apDepAirportId = airportDeparture.id',
            array('airportDepartureICAO' => 'code_icao', 'airportDepartureIATA' => 'code_iata'), 'left');

        $select->join(array('airportArrival' => 'library_airport'),
            'leg.apArrAirportId = airportArrival.id',
            array('airportArrivalICAO' => 'code_icao', 'airportArrivalIATA' => 'code_iata'), 'left');

        $select->join(array('country' => 'library_country'),
            'flightPermissionForm.countryId = country.id',
            array('countryName' => 'name', 'countryCode' => 'code'), 'left');

        $select->where(array('flightPermissionForm.headerId' => $id));
        $select->order(array('flightPermissionForm.legId ' . $select::ORDER_ASCENDING,
            'flightPermissionForm.id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

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
            $data[$row->legId]['permission'][$row->id] = array(
                'agentId' => $row->agentId,
                'agentName' => $row->agentName,
                'agentAddress' => $row->agentAddress,
                'agentMail' => $row->agentMail,
                'countryId' => $row->countryId,
                'countryName' => $row->countryName,
                'countryCode' => $row->countryCode,
                'typeOfPermission' => $row->typeOfPermission,
                'permission' => $row->permission,
                'comment' => $row->comment,
            );
        }

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByPermissionId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }

}
