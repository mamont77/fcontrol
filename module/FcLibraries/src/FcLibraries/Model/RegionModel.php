<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\RegionFilter;

/**
 * Class RegionModel
 * @package FcLibraries\Model
 */
class RegionModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_region';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new RegionFilter($this->adapter));
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
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param \FcLibraries\Filter\RegionFilter $object
     */
    public function add(RegionFilter $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $this->insert($data);
    }

    /**
     * @param \FcLibraries\Filter\RegionFilter $object
     * @throws \Exception
     */
    public function save(RegionFilter $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
