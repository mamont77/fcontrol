<?php

namespace FcLibrariesSearch\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
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
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getAdvancedSearchResult($text, $library)
    {
        $text = (string)$text;
        $this->table = (string)$library;
        $fields = array();

        switch ($library) {
            case 'library_aircraft':
                $fields = array('reg_number');
                break;
            case 'library_air_operator':
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
        }

        $rowSet = $this->select(array('text' => $text));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $text");
        }

        return $row;
    }
}
