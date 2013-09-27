<?php

namespace FcLogEvents\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

/**
 * Class SearchModel
 * @package FcLogEvents\Model
 */
class SearchModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table = 'log_table';

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
        $this->setTable('log_table');

        if ($object->dateFrom != '') {
            $object->dateFrom = \DateTime::createFromFormat('d-m-Y', $object->dateFrom)->getTimestamp();
        }
        if ($object->dateTo != '') {
            $object->dateTo = \DateTime::createFromFormat('d-m-Y', $object->dateTo)->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);

        $select->columns(array('id', 'message', 'priority', 'priorityName', 'username', 'url', 'ipaddress', 'timestamp'));

        if ($object->dateFrom != '' && $object->dateTo != '') {
            $select->where->between('log_table.timestamp', $object->dateFrom, $object->dateTo);
        } else {
            if ($object->dateFrom != '') {
                $select->where->greaterThanOrEqualTo('log_table.timestamp', $object->dateFrom);
            }

            if ($object->dateTo != '') {
                $select->where->lessThanOrEqualTo('log_table.timestamp', $object->dateTo);
            }
        }

        if ($object->priority) {
            $select->where->equalTo('log_table.priority', (int)$object->priority);
        }

        if ($object->username != '') {
            $select->where->like('log_table.username', $object->username . '%');
        }

        $select->order('timestamp ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();


        return $resultSet;
    }
}
