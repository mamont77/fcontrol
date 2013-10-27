<?php
/**
 * @namespace
 */
namespace FcLogEvents\Model;

use Zend\Db\Sql\Delete;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
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
        $select->columns(array('id', 'message', 'priority', 'priorityName',
            'username', 'url', 'ipaddress', 'timestamp', 'component'));
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    /**
     * @param $period
     */
    public function cleaningOldData($period)
    {
        $timeCutOff = mktime(0, 0, 0, date('m'), date('d') - $period, date('Y'));

        $delete = new Delete();
        $delete->from($this->table);
        $delete->where->lessThanOrEqualTo($this->table . '.timestamp', new Expression('FROM_UNIXTIME(?)', $timeCutOff));
        $this->deleteWith($delete);
    }
}
