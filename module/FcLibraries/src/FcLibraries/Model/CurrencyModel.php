<?php

namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\CurrencyFilter;

class CurrencyModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_currency';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new CurrencyFilter($this->adapter));
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
     * @param \FcLibraries\Filter\CurrencyFilter $object
     */
    public function add(CurrencyFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'currency' => $object->currency,
        );
        $this->insert($data);
    }

    /**
     * @param \FcLibraries\Filter\CurrencyFilter $object
     * @throws \Exception
     */
    public function save(CurrencyFilter $object)
    {
        $data = array(
            'name' => $object->name,
            'currency' => $object->currency,
        );
        $id = (int)$object->id;
        if ($this->get($id)) {
            $this->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
