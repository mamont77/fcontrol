<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\HotelFilter;

/**
 * Class HotelModel
 * @package FcFlight\Model
 */
class HotelModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightHotelForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new HotelFilter($this->adapter));
        $this->initialize();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function get($id)
    {

    }

    /**
     * @param HotelFilter $object
     * @return array
     */
    public function add(HotelFilter $object)
    {

    }

    /**
     * @param HotelFilter $object
     * @throws \Exception
     */
    public function save(HotelFilter $object)
    {

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

    }

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByHotelId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }

}
