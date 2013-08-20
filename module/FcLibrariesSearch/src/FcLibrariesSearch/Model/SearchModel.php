<?php

namespace FcLibrariesSearch\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class SearchModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table = '';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $text
     * @param $library
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function findAdvancedSearchResult($text, $library)
    {
        $text = (string)$text;
        $this->table = (string)$library;

        $select = new Select();
        $select->from($this->table);

        switch ($library) {
            case 'library_aircraft':
                $select->columns(array('id', 'aircraft_type', 'reg_number'));
                $select->join(array('library_aircraft_type' => 'library_aircraft_type'),
                    'library_aircraft_type.id = library_aircraft.aircraft_type',
                    array('aircraft_type_name' => 'name'), 'left');
                $select->where->like('reg_number', $text . '%');
                $select->order('reg_number ' . Select::ORDER_ASCENDING);
                break;

            case 'library_air_operator':
                $select->columns(array('id', 'name', 'short_name', 'code_icao', 'code_iata'));
                $select->join(array('library_country' => 'library_country'),
                    'library_country.id = library_air_operator.country',
                    array('country_name' => 'name'), 'left');
                $select->where->like('library_air_operator.name', $text . '%')
                    ->or
                    ->like('library_air_operator.short_name', $text . '%')
                    ->or
                    ->like('library_air_operator.code_icao', $text . '%')
                    ->or
                    ->like('library_air_operator.code_iata', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_airport':
                $fields = array('name', 'short_name', 'code_icao', 'code_iata');
                break;

            case 'library_country':
                $fields = array('name', 'code');
                break;

            case 'library_currency':
                $fields = array('name', 'currency');
                break;

            case 'library_kontragent':
                $fields = array('name', 'short_name', 'sita');
                break;

            case 'library_city':
            case 'library_region':
            case 'library_unit':
                $fields = array('name');
                break;

            default:
                // skip it
                break;
        }

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
