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
        $this->setTable('flightBaseHeaderForm');

        if ($object->dateOrderFrom != '') {
            $object->dateOrderFrom = \DateTime::createFromFormat('d-m-Y', $object->dateOrderFrom)->getTimestamp();
        }
        if ($object->dateOrderTo != '') {
            $object->dateOrderTo = \DateTime::createFromFormat('d-m-Y', $object->dateOrderTo)->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);

        $select->columns(array(
            'id',
            'refNumberOrder',
            'dateOrder',
            'kontragent',
            'airOperator',
            'aircraftId',
            'alternativeAircraftId1',
            'alternativeAircraftId2',
            'status',
        ));

        $select->join(array('flightLegForm' => 'flightLegForm'),
            'flightBaseHeaderForm.id = flightLegForm.headerId',
            array(
                'flightNumber' => 'flightNumber',
                'apDepAirportId' => 'apDepAirportId',
                'apDepTime' => 'apDepTime',
                'apArrAirportId' => 'apArrAirportId',
                'apArrTime' => 'apArrTime',
            ),
            'right');

        $select->join(array('libraryKontragent' => 'library_kontragent'),
            'libraryKontragent.id = flightBaseHeaderForm.kontragent',
            array('kontragentShortName' => 'short_name'), 'left');

        $select->join(array('libraryAirOperator' => 'library_air_operator'),
            'libraryAirOperator.id = flightBaseHeaderForm.airOperator',
            array('airOperatorShortName' => 'short_name'), 'left');

        $select->join(array('libraryAircraft' => 'library_aircraft'),
            'libraryAircraft.id = flightBaseHeaderForm.aircraftId',
            array('aircraftTypeId' => 'aircraft_type', 'aircraftName' => 'reg_number'), 'left');

        $select->join(array('libraryAircraftType' => 'library_aircraft_type'),
            'libraryAircraftType.id = libraryAircraft.aircraft_type',
            array('aircraftTypeName' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft1' => 'library_aircraft'),
            'libraryAlternativeAircraft1.id = flightBaseHeaderForm.alternativeAircraftId1',
            array('alternativeAircraftTypeId1' => 'aircraft_type', 'alternativeAircraftName1' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft1' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft1.id = libraryAlternativeAircraft1.aircraft_type',
            array('alternativeAircraftTypeName1' => 'name'), 'left');

        $select->join(array('libraryAlternativeAircraft2' => 'library_aircraft'),
            'libraryAlternativeAircraft2.id = flightBaseHeaderForm.alternativeAircraftId2',
            array('alternativeAircraftTypeId2' => 'aircraft_type', 'alternativeAircraftName2' => 'reg_number'), 'left');

        $select->join(array('libraryAlternativeTypeAircraft2' => 'library_aircraft_type'),
            'libraryAlternativeTypeAircraft2.id = libraryAlternativeAircraft2.aircraft_type',
            array('alternativeAircraftTypeName2' => 'name'), 'left');

        $select->join(array('author' => 'user'),
            'author.user_id = flightBaseHeaderForm.authorId',
            array('authorName' => 'username'), 'left');

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
                ->like('libraryKontragent.name', $object->customer . '%')
                ->OR
                ->like('libraryKontragent.short_name', $object->customer . '%')
                ->UNNEST;
        }

        if ($object->airOperator != '') {
            $select->where
                ->NEST
                ->like('libraryAirOperator.name', $object->airOperator . '%')
                ->OR
                ->like('libraryAirOperator.short_name', $object->airOperator . '%')
                ->UNNEST;
        }

        if ($object->aircraft != '') {
            $select->where
                ->NEST
                ->like('libraryAircraftType.name', $object->aircraft . '%')
                ->OR
                ->like('libraryAircraft.reg_number', $object->aircraft . '%')
                ->OR
                ->like('libraryAlternativeTypeAircraft1.name', $object->aircraft . '%')
                ->OR
                ->like('libraryAlternativeAircraft1.reg_number', $object->aircraft . '%')
                ->OR
                ->like('libraryAlternativeTypeAircraft2.name', $object->aircraft . '%')
                ->OR
                ->like('libraryAlternativeAircraft2.reg_number', $object->aircraft . '%')
                ->UNNEST;
        }

        if ($object->flightNumber != '') {
            $select->where->like('flightLegForm.flightNumber', $object->flightNumber . '%');
        }

        $select->group('refNumberOrder');
        $select->order('dateOrder ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();


        return $resultSet;
    }
}
