<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use FcLibraries\Model\LibraryTable;

class RegionTable extends LibraryTable
{
    /**
     * @var string
     */
    protected $table = 'library_region';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter, Region $region)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype($region);
        $this->initialize();
    }

    /**
     * @param Region $object
     */
    public function add(Region $object)
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
    public function save(Region $object)
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
