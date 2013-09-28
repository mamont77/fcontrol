<?php

namespace FcLogEvents\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcLogEvents\Filter\FcLogEventsFilter;

/**
 * Class FcLogEventsModel
 * @package FcLogEvents\Model
 */
class FcLogEventsModel extends AbstractTableGateway
{
    protected $table = 'logs';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new FcLogEventsFilter($this->adapter));

        $this->initialize();
    }

    /**
     * @param Select $select
     * @return mixed
     */
    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        $select->from($this->table);
        $select->columns(array('id', 'message', 'priority', 'priorityName', 'username', 'url', 'ipaddress', 'timestamp'));
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
