<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\TypeOfPermissionFilter;

/**
 * Class TypeOfPermissionModel
 * @package FcFlight\Model
 */
class TypeOfPermissionModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightTypeOfPermissionForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new TypeOfPermissionFilter($this->adapter));
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
            'slotApDep',
            'slotApArr',
            'fpl',
            'ppl',
        ));

        $select->join(array('airport' => 'library_airport'),
            'flightTypeOfPermissionForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param TypeOfPermissionFilter $object
     * @return array
     */
    public function add(TypeOfPermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'airportId' => (int)$object->airportId,
            'slotApDep' => (int)$object->slotApDep,
            'slotApArr' => (int)$object->slotApArr,
            'fpl' => (int)$object->fpl,
            'ppl' => (int)$object->ppl,
        );

        $this->insert($data);

        return array(
            'lastInsertValue' => $this->getLastInsertValue(),
        );
    }

    /**
     * @param TypeOfPermissionFilter $object
     * @throws \Exception
     */
    public function save(TypeOfPermissionFilter $object)
    {
        $data = array(
            'headerId' => (int)$object->headerId,
            'airportId' => (int)$object->airportId,
            'slotApDep' => (int)$object->slotApDep,
            'slotApArr' => (int)$object->slotApArr,
            'fpl' => (int)$object->fpl,
            'ppl' => (int)$object->ppl,
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
            'slotApDep',
            'slotApArr',
            'fpl',
            'ppl',
        ));

        $select->join(array('airport' => 'library_airport'),
            'flightTypeOfPermissionForm.airportId = airport.id',
            array('icao' => 'code_icao', 'iata' => 'code_iata', 'airportName' => 'name'), 'left');

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
            $data[$row->id]['slotApDep'] = $row->slotApDep;
            $data[$row->id]['slotApArr'] = $row->slotApArr;
            $data[$row->id]['fpl'] = $row->fpl;
            $data[$row->id]['ppl'] = $row->ppl;

            //Virtual fields
            $data[$row->id]['icao'] = $row->icao;
            $data[$row->id]['iata'] = $row->iata;
            $data[$row->id]['airportName'] = $row->airportName;
        }

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getHeaderRefNumberOrderByTypeOfPermissionId($id)
    {
        $row = $this->get($id);
        $headerModel = new FlightHeaderModel($this->getAdapter());

        return $headerModel->getRefNumberOrderById($row->headerId);
    }
}
