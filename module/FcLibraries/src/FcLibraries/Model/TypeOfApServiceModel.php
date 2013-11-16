<?php
/**
 * @namespace
 */
namespace FcLibraries\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLibraries\Model\BaseModel;
use FcLibraries\Filter\TypeOfApServiceFilter;

/**
 * Class TypeOfApServiceModel
 * @package FcLibraries\Model
 */
class TypeOfApServiceModel extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'library_type_of_ap_service';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new TypeOfApServiceFilter($this->adapter));
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
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param TypeOfApServiceFilter $object
     * @return int
     */
    public function add(TypeOfApServiceFilter $object)
    {
        $data = array(
            'name' => $object->name,
        );
        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param \FcLibraries\Filter\TypeOfApServiceFilter $object
     * @throws \Exception
     */
    public function save(TypeOfApServiceFilter $object)
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