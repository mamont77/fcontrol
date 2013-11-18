<?php
/**
 * @namespace
 */
namespace FcFlight\Model;

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
    protected $_table = '';

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
        $this->_table = $table;
    }

    /**
     * @param $object
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function findSearchResult($object)
    {
        $this->setTable('flightBaseHeaderForm');

        if ($object->dateOrderFrom != '') {
            $object->dateOrderFrom = \DateTime::createFromFormat('d-m-Y', $object->dateOrderFrom)->getTimestamp();
        }
        if ($object->dateOrderTo != '') {
            $object->dateOrderTo = \DateTime::createFromFormat('d-m-Y', $object->dateOrderTo)->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);

        $select->columns(array('id', 'refNumberOrder', 'dateOrder', 'kontragent', 'airOperator', 'aircraft', 'status'));

        $select->join(array('library_kontragent' => 'library_kontragent'),
            'library_kontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');
        $select->join(array('library_air_operator' => 'library_air_operator'),
            'library_air_operator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');
        $select->join(array('library_aircraft' => 'library_aircraft'),
            'library_aircraft.reg_number = flightBaseHeaderForm.aircraft',
            array('aircraftType' => 'aircraft_type'), 'left');
        $select->join(array('library_aircraft_type' => 'library_aircraft_type'),
            'library_aircraft_type.id = library_aircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');

        if ($object->dateOrderFrom != '' && $object->dateOrderTo != '') {
            $select->where->between('flightBaseHeaderForm.dateOrder', $object->dateOrderFrom, $object->dateOrderTo);
        } else {
            if ($object->dateOrderFrom != '') {
                $select->where->greaterThanOrEqualTo('flightBaseHeaderForm.dateOrder', $object->dateOrderFrom);
            }

            if ($object->dateOrderTo != '') {
                $select->where->lessThanOrEqualTo('flightBaseHeaderForm.dateOrder', $object->dateOrderTo);
            }
        }

        if ($object->status != '2') {
            $select->where->equalTo('flightBaseHeaderForm.status', (int)$object->status);
        }

        if ($object->customer != '') {
            $select->where
                ->NEST
                ->like('library_kontragent.name', $object->customer . '%')
                ->OR
                ->like('library_kontragent.short_name', $object->customer . '%')
                ->UNNEST;
        }

        if ($object->airOperator != '') {
            $select->where
                ->NEST
                ->like('library_air_operator.name', $object->airOperator . '%')
                ->OR
                ->like('library_air_operator.short_name', $object->airOperator . '%')
                ->UNNEST;
        }

        if ($object->aircraft != '') {
            $select->where
                ->NEST
                ->like('library_aircraft_type.name', $object->aircraft . '%')
                ->OR
                ->like('library_aircraft.reg_number', $object->aircraft . '%')
                ->UNNEST;
        }

        $select->order('dateOrder ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();


        return $resultSet;
    }
}
