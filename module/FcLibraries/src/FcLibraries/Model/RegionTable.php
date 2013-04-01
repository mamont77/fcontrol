<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use FcLibraries\Model\LibraryTable;
use FcLibraries\Filter\RegionFilter;

class RegionTable extends LibraryTable
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
     * @param Region $object
     */
    public function add(RegionFilter $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $this->insert($data);
    }

    /**
     * @param Region $object
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
