<?php
/**
 * @namespace
 */
namespace FcLogEvents\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
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
    protected $table = 'logs';

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
        $this->setTable('logs');

        if ($object->dateFrom != '') {
            $object->dateFrom = \DateTime::createFromFormat('d-m-Y', $object->dateFrom)
                ->setTime(00, 00, 00)
                ->getTimestamp();
        }
        if ($object->dateTo != '') {
            $object->dateTo = \DateTime::createFromFormat('d-m-Y', $object->dateTo)
                ->setTime(23, 59, 59)
                ->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);

        $select->columns(array('id', 'message', 'priority', 'priorityName',
            'username', 'url', 'ipaddress', 'timestamp', 'component'));

        if ($object->dateFrom != '' && $object->dateTo != '') {
            $select->where->between('logs.timestamp',
                new Expression('FROM_UNIXTIME(?)', $object->dateFrom),
                new Expression('FROM_UNIXTIME(?)', $object->dateTo));
        } else {
            if ($object->dateFrom != '') {
                $select->where->greaterThanOrEqualTo('logs.timestamp',
                    new Expression('FROM_UNIXTIME(?)', $object->dateFrom));
            }

            if ($object->dateTo != '') {
                $select->where->lessThanOrEqualTo('logs.timestamp',
                    new Expression('FROM_UNIXTIME(?)', $object->dateTo));
            }
        }

        if ($object->priority) {
            $select->where->equalTo('logs.priority', (int)$object->priority);
        }

        if ($object->username != '') {
            $select->where->like('logs.username', $object->username . '%');
        }

        $select->order('timestamp ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();


        return $resultSet;
    }
}
