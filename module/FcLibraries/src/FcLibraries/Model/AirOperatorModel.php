<?php
/**
 * @namespace
 */
namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\AirOperatorFilter;

/**
 * Class AirOperatorModel
 * @package FcLibraries\Model
 */
class AirOperatorModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_air_operator';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AirOperatorFilter($this->adapter));
        $this->initialize();
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('id', 'name', 'short_name', 'code_icao', 'code_iata'));
        $select->join(array('c' => 'library_country'),
            'c.id = library_air_operator.country',
            array('country_name' => 'name'), 'left');
//        $select->order('library_country.code ASC');
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
        $select->columns(array('id', 'name', 'short_name', 'code_icao', 'code_iata', 'country'));

        $select->join(array('c' => 'library_country'),
            'c.id = library_air_operator.country',
            array('country_name' => 'name'), 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param AirOperatorFilter $object
     * @return int
     */
    public function add(AirOperatorFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'country' => $object->country,
        );
        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param AirOperatorFilter $object
     * @throws \Exception
     */
    public function save(AirOperatorFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'code_icao' => $object->code_icao,
            'code_iata' => $object->code_iata,
            'country' => $object->country,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
