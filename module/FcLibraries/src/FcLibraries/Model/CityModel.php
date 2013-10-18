<?php
/**
 * @namespace
 */
namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\CityFilter;

/**
 * Class CityModel
 * @package FcLibraries\Model
 */
class CityModel extends BaseModel
{
    protected $table = 'library_city';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new CityFilter($this->adapter));

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
        $select->columns(array('id', 'name', 'country_id'));
        $select->join(array('c' => 'library_country'),
            'c.id = library_city.country_id',
            array('country_name' => 'name'), 'left');
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
        $select->columns(array('id', 'name', 'country_id'));

        $select->join(array('c' => 'library_country'),
            'c.id = library_city.country_id',
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
     * @param CityFilter $object
     * @return int
     */
    public function add(CityFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'country_id' => $object->country_id,
        );
        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param \FcLibraries\Filter\CityFilter $object
     * @throws \Exception
     */
    public function save(CityFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'country_id' => $object->country_id,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

}
