<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\KontragentFilter;

class KontragentModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_kontragent';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new KontragentFilter($this->adapter));
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
     * @param KontragentFilter $object
     */
    public function add(KontragentFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'address' => $object->address,
            'phone1' => $object->phone1,
            'phone2' => $object->phone2,
            'phone3' => $object->phone3,
            'fax' => $object->fax,
            'mail' => $object->mail,
            'sita' => $object->sita,
        );
        $this->insert($data);
    }

    /**
     * @param KontragentFilter $object
     * @throws \Exception
     */
    public function save(KontragentFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'short_name' => $object->short_name,
            'address' => $object->address,
            'phone1' => $object->phone1,
            'phone2' => $object->phone2,
            'phone3' => $object->phone3,
            'fax' => $object->fax,
            'mail' => $object->mail,
            'sita' => $object->sita,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
