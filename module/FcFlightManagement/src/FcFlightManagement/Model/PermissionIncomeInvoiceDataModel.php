<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class PermissionIncomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class PermissionIncomeInvoiceDataModel extends PermissionIncomeInvoiceMainModel
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomePermissionData';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }

    /**
     * @param $data
     * @return int
     */
    public function add($data)
    {
        $data['dateDep'] = \DateTime::createFromFormat('d-m-Y', $data['dateDep'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateArr'] = \DateTime::createFromFormat('d-m-Y', $data['dateArr'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->permissionIncomeInvoiceDataTableFieldsMap);

        foreach ($fields as $key => &$field) {
            if (isset($data[$key])) {
                $field = $data[$key];
            } else {
                unset($fields[$key]);
            }
        }

        $this->insert($fields);

        return $this->getLastInsertValue();
    }

    public function getByInvoiceId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->permissionIncomeInvoiceDataTableFieldsMap);

        $select->join(
            array('permissionPreInvoiceMain' => $this->permissionPreInvoiceMainTableName),
            $this->table . '.preInvoiceId = permissionPreInvoiceMain.id',
            $this->permissionPreInvoiceTableFieldsMap,
            'left');

        $select->join(
            array('flight' => $this->flightTableName),
            'permissionPreInvoiceMain.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left');

        $select->join(
            array('leg' => $this->legTableName),
            'permissionPreInvoiceMain.legId = leg.id',
            $this->legTableFieldsMap,
            'left');

        $select->join(
            array('flightCustomer' => 'library_kontragent'),
            'flight.kontragent = flightCustomer.id',
            array(
                'flightCustomerName' => 'name',
                'flightCustomerShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('flightAirOperator' => 'library_air_operator'),
            'flight.airOperator = flightAirOperator.id',
            array(
                'flightAirOperatorName' => 'name',
                'flightAirOperatorShortName' => 'short_name',
                'flightAirOperatorICAO' => 'code_icao',
                'flightAirOperatorIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('legAirportDep' => 'library_airport'),
            'leg.apDepAirportId = legAirportDep.id',
            array(
                'legAirportDepName' => 'name',
                'legAirportDepShortName' => 'short_name',
                'legAirportDepICAO' => 'code_icao',
                'legAirportDepIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('legAirportArr' => 'library_airport'),
            'leg.apArrAirportId = legAirportArr.id',
            array(
                'legAirportArrName' => 'name',
                'legAirportArrShortName' => 'short_name',
                'legAirportArrICAO' => 'code_icao',
                'legAirportArrIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceCountry' => 'library_country'),
            'permissionPreInvoiceMain.countryId = preInvoiceCountry.id',
            array(
                'preInvoiceCountryName' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataUnit' => 'library_unit'),
            $this->table . '.unitId = incomeInvoiceDataUnit.id',
            array(
                'incomeInvoiceDataUnitName' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataAircraft' => 'library_aircraft'),
            $this->table . '.aircraftId = incomeInvoiceDataAircraft.id',
            array(
                'incomeInvoiceDataAircraftTypeId' => 'aircraft_type',
                'incomeInvoiceDataAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataAircraftType' => 'library_aircraft_type'),
            'incomeInvoiceDataAircraft.aircraft_type = incomeInvoiceDataAircraftType.id',
            array(
                'incomeInvoiceDataAircraftTypeName' => 'name',
            ),
            'left');

        $select->where(array($this->table . '.invoiceId' => $id));
        $select->order(array($this->table . '.id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

//        \Zend\Debug\Debug::dump($resultSet);
//
//        foreach ($resultSet as $row) {
//            \Zend\Debug\Debug::dump($row);
//
//        }

        return $resultSet;
    }
}
