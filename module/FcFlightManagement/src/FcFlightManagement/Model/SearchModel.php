<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

/**
 * Class SearchModel
 * @package FcFlight\Model
 */
class SearchModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param $object
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function findSearchResult($object)
    {

    }
}
