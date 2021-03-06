<?php
/**
 * @namespace
 */
namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\CountryFilter;

/**
 * Class CountryModel
 * @package FcLibraries\Model
 */
class CountryModel extends BaseModel
{
    protected $table = 'library_country';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new CountryFilter($this->adapter));

        $this->initialize();
    }

    /**
     * @param Select $select
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('id', 'name', 'code'));
        $select->join(array('r' => 'library_region'),
            'r.id = library_country.region_id',
            array('region_name' => 'name') , 'left');
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
        $select->columns(array('id', 'name', 'code', 'region_id'));

        $select->join(array('r' => 'library_region'),
            'r.id = library_country.region_id',
            array('region_name' => 'name') , 'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param CountryFilter $object
     * @return int
     */
    public function add(CountryFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'region_id' => $object->region_id,
            'code' => $object->code,
        );
        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param \FcLibraries\Filter\CountryFilter $object
     * @throws \Exception
     */
    public function save(CountryFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'region_id' => $object->region_id,
            'code' => $object->code,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

}
