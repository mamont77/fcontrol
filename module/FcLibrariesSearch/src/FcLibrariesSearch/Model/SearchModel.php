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

            case 'library_aircraft_type':
                $select->columns(array('id', 'name'));
                $select->where->like('name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
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
                $select->columns(array('id', 'name', 'short_name', 'code_icao', 'code_iata'));
                $select->join(array('city' => 'library_city'),
                    'library_airport.city_id = city.id',
                    array('city_name' => 'name'), 'left');
                $select->join(array('country' => 'library_country'),
                    'city.country_id = country.id',
                    array('country_name' => 'name'), 'left');
                $select->join(array('region' => 'library_region'),
                    'country.region_id = region.id',
                    array('region_name' => 'name'), 'left');
                $select->where->like('library_airport.name', $text . '%')
                    ->or
                    ->like('library_airport.short_name', $text . '%')
                    ->or
                    ->like('library_airport.code_icao', $text . '%')
                    ->or
                    ->like('library_airport.code_iata', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_city':
                $select->columns(array('id', 'name', 'country_id'));
                $select->join(array('country' => 'library_country'),
                    'country.id = library_city.country_id',
                    array('country_name' => 'name'), 'left');
                $select->where->like('library_city.name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_country':
                $select->columns(array('id', 'name', 'code'));
                $select->join(array('region' => 'library_region'),
                    'region.id = library_country.region_id',
                    array('region_name' => 'name'), 'left');
                $select->where->like('library_country.name', $text . '%')
                    ->or
                    ->like('library_country.code', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_currency':
                $select->columns(array('id', 'name', 'currency'));
                $select->where->like('name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_kontragent':
                $select->columns(array('id',
                    'name', 'short_name', 'address', 'phone1', 'phone2', 'phone3', 'fax', 'mail', 'sita'));
                $select->where->like('name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_region':
                $select->columns(array('id', 'name'));
                $select->where->like('name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
                break;

            case 'library_unit':
                $select->columns(array('id', 'name'));
                $select->where->like('name', $text . '%');
                $select->order('name ' . Select::ORDER_ASCENDING);
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
