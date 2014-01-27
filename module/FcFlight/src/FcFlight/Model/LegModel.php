<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\LegFilter;

/**
 * Class LegModel
 * @package FcFlight\Model
 */
class LegModel extends AbstractTableGateway
{

    /**
     * @var string
     */

    public $table = 'flightLegForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new LegFilter($this->adapter));
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
            'flightNumber',
            'apDepAirportId',
            'apDepTime',
            'apArrAirportId',
            'apArrTime'));

        $select->join(array('ApDepAirport' => 'library_airport'),
            'ApDepAirport.id = flightLegForm.apDepAirportId',
            array('apDepName' => 'name', 'apDepIcao' => 'code_icao', 'apDepIata' => 'code_iata'), 'left');

        $select->join(array('ApDepCity' => 'library_city'),
            'ApDepCity.id = ApDepAirport.city_id',
            array('apDepCityId' => 'id', 'apDepCityName' => 'name'), 'left');

        $select->join(array('ApDepCountry' => 'library_country'),
            'ApDepCountry.id = ApDepCity.country_id',
            array('apDepCountryId' => 'id', 'apDepCountryName' => 'name', 'apDepCountryCode' => 'code'), 'left');

        $select->join(array('ApArrAirport' => 'library_airport'),
            'ApArrAirport.id = flightLegForm.apArrAirportId',
            array('apArrName' => 'name', 'apArrIcao' => 'code_icao', 'apArrIata' => 'code_iata'), 'left');

        $select->join(array('ApArrCity' => 'library_city'),
            'ApArrCity.id = ApArrAirport.city_id',
            array('apArrCityId' => 'id', 'apArrCityName' => 'name'), 'left');

        $select->join(array('ApArrCountry' => 'library_country'),
            'ApArrCountry.id = ApArrCity.country_id',
            array('apArrCountryId' => 'id', 'apArrCountryName' => 'name', 'apArrCountryCode' => 'code'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->apDepTime = date('d-m-Y H:i', $row->apDepTime);
        $row->apArrTime = date('d-m-Y H:i', $row->apArrTime);

        return $row;
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
            'flightNumber',
            'apDepAirportId',
            'apDepTime',
            'apArrAirportId',
            'apArrTime'));

        $select->join(array('ApDepAirport' => 'library_airport'),
            'ApDepAirport.id = flightLegForm.apDepAirportId',
            array('apDepName' => 'name', 'apDepIcao' => 'code_icao', 'apDepIata' => 'code_iata'), 'left');

        $select->join(array('ApDepCity' => 'library_city'),
            'ApDepCity.id = ApDepAirport.city_id',
            array('apDepCityId' => 'id', 'apDepCityName' => 'name'), 'left');

        $select->join(array('ApDepCountry' => 'library_country'),
            'ApDepCountry.id = ApDepCity.country_id',
            array('apDepCountryId' => 'id', 'apDepCountryName' => 'name', 'apDepCountryCode' => 'code'), 'left');

        $select->join(array('ApArrAirport' => 'library_airport'),
            'ApArrAirport.id = flightLegForm.apArrAirportId',
            array('apArrName' => 'name', 'apArrIcao' => 'code_icao', 'apArrIata' => 'code_iata'), 'left');

        $select->join(array('ApArrCity' => 'library_city'),
            'ApArrCity.id = ApArrAirport.city_id',
            array('apArrCityId' => 'id', 'apArrCityName' => 'name'), 'left');

        $select->join(array('ApArrCountry' => 'library_country'),
            'ApArrCountry.id = ApArrCity.country_id',
            array('apArrCountryId' => 'id', 'apArrCountryName' => 'name', 'apArrCountryCode' => 'code'), 'left');

        $select->where(array('headerId' => $id));
        $select->order(array('apDepTime ' . $select::ORDER_ASCENDING, 'id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $data = array();
        foreach ($resultSet as $row) {
            //Real fields
            $data[$row->id]['id'] = $row->id;
            $data[$row->id]['headerId'] = $row->headerId;
            $data[$row->id]['flightNumber'] = $row->flightNumber;
            $data[$row->id]['apDepAirportId'] = $row->apDepAirportId;
            $data[$row->id]['apDepTime'] = date('d-m-Y H:i', $row->apDepTime);
            $data[$row->id]['apArrAirportId'] = $row->apArrAirportId;
            $data[$row->id]['apArrTime'] = date('d-m-Y H:i', $row->apArrTime);
            //Virtual fields from join
             $data[$row->id]['apDepIcao'] = $row->apDepIcao;
            $data[$row->id]['apDepIata'] = $row->apDepIata;
            $data[$row->id]['apArrIcao'] = $row->apArrIcao;
            $data[$row->id]['apArrIata'] = $row->apArrIata;
            $data[$row->id]['apDepCountryId'] = $row->apDepCountryId;
            $data[$row->id]['apArrCountryId'] = $row->apArrCountryId;
        }

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getLastByHeaderId($id)
    {
        $id = (int)$id;
        $select = $this->getSql()->select();
        $select->where(array('headerId' => $id));
        $select->order(array('id ' . $select::ORDER_DESCENDING));
        $select->limit(1);
        $row = $this->selectWith($select)->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->apDepTime = date('d-m-Y H:i', $row->apDepTime);

        return $row;
    }

    /**
     * @param $object
     * @return array
     */
    public function add($object)
    {
        $apDepTime = \DateTime::createFromFormat('d-m-Y H:i', $object->apDepTime)
            ->getTimestamp();
        $apArrTime = \DateTime::createFromFormat('d-m-Y H:i', $object->apArrTime)
            ->getTimestamp();

        $data = array(
            'headerId' => (int)$object->headerId,
            'flightNumber' => (string)$object->flightNumber,
            'apDepAirportId' => (int)$object->apDepAirportId,
            'apDepTime' => (string)$apDepTime,
            'apArrAirportId' => (int)$object->apArrAirportId,
            'apArrTime' => (string)$apArrTime,
        );
        $hash = 'Dep ' . $object->apDepTime . ', Arr ' . $object->apArrTime;

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
            'hash' => $hash,
        );
    }

    /**
     * @param LegFilter $object
     * @return string
     * @throws \Exception
     */
    public function save(LegFilter $object)
    {
        $apDepTime = \DateTime::createFromFormat('d-m-Y H:i', $object->apDepTime)
            ->getTimestamp();
        $apArrTime = \DateTime::createFromFormat('d-m-Y H:i', $object->apArrTime)
            ->getTimestamp();

        $data = array(
            'headerId' => (int)$object->headerId,
            'flightNumber' => (string)$object->flightNumber,
            'apDepAirportId' => (int)$object->apDepAirportId,
            'apDepTime' => (string)$apDepTime,
            'apArrAirportId' => (int)$object->apArrAirportId,
            'apArrTime' => (string)$apArrTime,
        );
        $hash = 'Dep ' . $object->apDepTime . ', Arr ' . $object->apArrTime;

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
    public function getHeaderRefNumberOrderByLegId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }

    /**
     * @param $id
     * @return bool
     */
    public function legIsLast($id)
    {
        $id = (int)$id;

        $leg = $this->get($id);
        $headerId = $leg->headerId;

        $select = new Select();
        $select->from($this->table);

        $select->columns(array('rows' => new \Zend\Db\Sql\Expression('COUNT(*)')));

        $select->where(array('headerId' => $headerId));
        $select->where->greaterThanOrEqualTo($this->table . '.id', $id);
        $select->order(array('id ' . $select::ORDER_ASCENDING));

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return ($resultSet == 1) ? true : false;
    }

    /**
     * @param $id
     * @return array
     */
    public function getListByHeaderId($id)
    {
        $data = $this->getByHeaderId($id);

        $result = array();
        foreach ($data as $row) {
            $result[$row['id']] = $row['apDepIcao'] . ' / ' . $row['apDepIata'] . ' ⇒ '
                . $row['apArrIcao'] . ' / ' . $row['apArrIata'];
        }

        return $result;
    }
}
