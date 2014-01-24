<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class PermissionIncomeInvoiceSearchModel
 * @package FcFlightManagement\Model
 */
class PermissionIncomeInvoiceSearchModel extends BaseModel
{
    /**
     * Name of the variable оставлено для совместимости с AbstractTableGateway
     *
     * @var string
     */
    public $table = 'flightPermissionForm';

    /**
     * @param array $data
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function findByParams($data = array())
    {
        if ($data['dateFrom'] != '') {
            $data['dateFrom'] = \DateTime::createFromFormat('d-m-Y', $data['dateFrom'])
                ->setTime(0, 0)->getTimestamp();
        }
        if ($data['dateTo'] != '') {
            $data['dateTo'] = \DateTime::createFromFormat('d-m-Y', $data['dateTo'])
                ->setTime(0, 0)->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);
        $select->columns($this->permissionPreInvoiceTableFieldsMap);

        $select->join(
            array('flight' => $this->flightTableName),
            $this->table . '.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left');

        $select->join(
            array('leg' => $this->legTableName),
            $this->table . '.legId = leg.id',
            $this->legTableFieldsMap,
            'left');

        $select->join(
            array('incomeInvoiceData' => $this->permissionIncomeInvoiceDataTableName),
            $this->table . '.id = incomeInvoiceData.preInvoiceId',
            $this->permissionIncomeInvoiceDataTableFieldsMap,
            'left');

        $select->join(
            array('incomeInvoiceMain' => $this->permissionIncomeInvoiceMainTableName),
            'incomeInvoiceData.invoiceId = incomeInvoiceMain.id',
            $this->permissionIncomeInvoiceMainTableFieldsMap,
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
            array('flightAircraft' => 'library_aircraft'),
            'flight.aircraftId = flightAircraft.id',
            array(
                'flightAircraftTypeId' => 'aircraft_type',
                'flightAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('flightAircraftType' => 'library_aircraft_type'),
            'flightAircraft.aircraft_type = flightAircraftType.id',
            array(
                'flightAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('preInvoiceAirportDep' => 'library_airport'),
            'leg.apDepAirportId = preInvoiceAirportDep.id',
            array(
                'preInvoiceAirportDepName' => 'name',
                'preInvoiceAirportDepShortName' => 'short_name',
                'preInvoiceAirportDepICAO' => 'code_icao',
                'preInvoiceAirportDepIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceAirportArr' => 'library_airport'),
            'leg.apArrAirportId = preInvoiceAirportArr.id',
            array(
                'preInvoiceAirportArrName' => 'name',
                'preInvoiceAirportArrShortName' => 'short_name',
                'preInvoiceAirportArrICAO' => 'code_icao',
                'preInvoiceAirportArrIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceCountry' => 'library_country'),
            $this->table . '.countryId = preInvoiceCountry.id',
            array(
                'preInvoiceCountryName' => 'name',
            ),
            'left');

        $select->join(
            array('preInvoiceAgent' => 'library_kontragent'),
            $this->table . '.agentId = preInvoiceAgent.id',
            array(
                'preInvoiceAgentName' => 'name',
                'preInvoiceAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataAircraft' => 'library_aircraft'),
            'incomeInvoiceData.aircraftId = incomeInvoiceDataAircraft.id',
            array(
                'incomeInvoiceDataAircraftTypeId' => 'aircraft_type',
                'incomeInvoiceDataName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataAircraftType' => 'library_aircraft_type'),
            'incomeInvoiceDataAircraft.aircraft_type = incomeInvoiceDataAircraftType.id',
            array(
                'incomeInvoiceDataAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataUnit' => 'library_unit'),
            'incomeInvoiceData.unitId = incomeInvoiceDataUnit.id',
            array(
                'incomeInvoiceDataUnitName' => 'name',
            ),
            'left');

        $select->where->equalTo('flight.isYoungest', 1);

        if ($data['dateFrom'] != '' && $data['dateTo'] != '') {
            $select->where
                ->NEST
                ->between('leg.apDepTime', $data['dateFrom'], $data['dateTo'])
                ->OR
                ->between('leg.apArrTime', $data['dateFrom'], $data['dateTo'])
                ->UNNEST;
        } else {
            if ($data['dateFrom'] != '') {
                $select->where
                    ->NEST
                    ->greaterThanOrEqualTo('leg.apDepTime', $data['dateFrom'])
                    ->OR
                    ->greaterThanOrEqualTo('leg.apArrTime', $data['dateFrom'])
                    ->UNNEST;
            }

            if ($data['dateTo'] != '') {
                $select->where
                    ->NEST
                    ->lessThanOrEqualTo('leg.apDepTime', $data['dateFrom'])
                    ->OR
                    ->lessThanOrEqualTo('leg.apArrTime', $data['dateFrom'])
                    ->UNNEST;
            }
        }

        if ($data['aircraftId'] != '') {
            $select->where
                ->NEST
                ->equalTo('flight.aircraftId', $data['aircraftId'])
                ->OR
                ->equalTo('flight.alternativeAircraftId1', $data['aircraftId'])
                ->OR
                ->equalTo('flight.alternativeAircraftId2', $data['aircraftId'])
                ->UNNEST;
        }

        if ($data['agentId'] != '') {
            $select->where->equalTo($this->table . '.agentId', $data['agentId']);
        }

        if ($data['agentId'] != '') {
            $select->where->equalTo($this->table . '.agentId', $data['agentId']);
        }

        if ($data['countryId'] != '') {
            $select->where->equalTo($this->table . '.countryId', $data['countryId']);
        }

        if ($data['airportDepId'] != '') {
            $select->where->equalTo('leg.airportDepId', $data['airportDepId']);
        }

        if ($data['airportArrId'] != '') {
            $select->where->equalTo('leg.airportArrId', $data['airportArrId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo('flight.kontragent', $data['customerId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo('flight.airOperator', $data['airOperatorId']);
        }

        if (!empty($data['rowsSelected'])) {
            $select->where->in($this->table . '.id', $data['rowsSelected']);
        }

        $select->where->isNotNull('flight.id');

        $select->order($this->table . '.id ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString()); exit;
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
