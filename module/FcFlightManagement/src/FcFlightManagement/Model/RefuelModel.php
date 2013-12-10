<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlightManagement\Filter\RefuelStep1Filter;

/**
 * Class RefuelModel
 * @package FcFlight\Model
 */
class RefuelModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @var array
     */
    protected $_tableFields = array(
        'refuelId' => 'id',
        'refuelHeaderId' => 'headerId',
        'refuelAgentId' => 'agentId',
        'refuelLegId' => 'legId',
        'refuelAirportId' => 'airportId',
        'refuelQuantityLtr' => 'quantityLtr',
        'refuelQuantityOtherUnits' => 'quantityOtherUnits',
        'refuelUnitId' => 'unitId',
        'refuelPriceUsd' => 'priceUsd',
        'refuelTotalPriceUsd' => 'totalPriceUsd',
        'refuelDate' => 'date',
        'refuelStatus' => 'status',
    );

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
     * @param array $data
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function findByParams($data = array())
    {
        $this->setTable('flightRefuelForm');

        if ($data['dateOrderFrom'] != '') {
            $data['dateOrderFrom'] = \DateTime::createFromFormat('d-m-Y', $data['dateOrderFrom'])->getTimestamp();
        }
        if ($data['dateOrderTo'] != '') {
            $data['dateOrderTo'] = \DateTime::createFromFormat('d-m-Y', $data['dateOrderTo'])->getTimestamp();
        }

        $select = new Select();
        $select->from($this->table);

        $select->columns($this->_tableFields);

        $select->join(
            array('flight' => 'flightBaseHeaderForm'),
            'flightRefuelForm.headerId = flight.id',
            array(
                'flightParentId' => 'parentId',
                'flightRefNumberOrder' => 'refNumberOrder',
                'flightDateOrder' => 'dateOrder',
                'flightAgentId' => 'kontragent',
                'flightAirOperator' => 'airOperator',
                'flightAircraftId' => 'aircraftId',
                'flightAlternativeAircraftId1' => 'alternativeAircraftId1',
                'flightAlternativeAircraftId2' => 'alternativeAircraftId2',
                'flightStatus' => 'status',
            ),
            'left');

        $select->join(
            array('refuelAgent' => 'library_kontragent'),
            'refuelAgent.id = flightRefuelForm.agentId',
            array(
                'refuelAgentName' => 'name',
                'refuelAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('refuelAirport' => 'library_airport'),
            'refuelAirport.id = flightRefuelForm.airportId',
            array(
                'refuelAirportName' => 'name',
                'refuelAirportShortName' => 'short_name',
                'refuelAirportICAO' => 'code_icao',
                'refuelAirportIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('flightAgent' => 'library_kontragent'),
            'flightAgent.id = flight.kontragent',
            array(
                'flightAgentName' => 'name',
                'flightAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('flightAirOperator' => 'library_air_operator'),
            'flightAirOperator.id = flight.airOperator',
            array(
                'flightAirOperatorName' => 'name',
                'flightAirOperatorShortName' => 'short_name',
                'flightAirOperatorICAO' => 'code_icao',
                'flightAirOperatorIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('flightAircraft' => 'library_aircraft'),
            'flightAircraft.id = flight.aircraftId',
            array(
                'flightAircraftTypeId' => 'aircraft_type',
                'flightAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('flightAircraftType' => 'library_aircraft_type'),
            'flightAircraftType.id = flightAircraft.aircraft_type',
            array(
                'flightAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('flightAlternativeAircraft1' => 'library_aircraft'),
            'flightAlternativeAircraft1.id = flight.alternativeAircraftId1',
            array(
                'flightAlternativeAircraftTypeId1' => 'aircraft_type',
                'flightAlternativeAircraftName1' => 'reg_number',
            ),
            'left');

        $select->join(
            array('flightAlternativeTypeAircraft1' => 'library_aircraft_type'),
            'flightAlternativeTypeAircraft1.id = flightAlternativeAircraft1.aircraft_type',
            array(
                'flightAlternativeAircraftTypeName1' => 'name',
            ),
            'left');

        $select->join(
            array('flightAlternativeAircraft2' => 'library_aircraft'),
            'flightAlternativeAircraft2.id = flight.alternativeAircraftId2',
            array(
                'flightAlternativeAircraftTypeId2' => 'aircraft_type',
                'flightAlternativeAircraftName2' => 'reg_number',
            ),
            'left');

        $select->join(
            array('flightAlternativeTypeAircraft2' => 'library_aircraft_type'),
            'flightAlternativeTypeAircraft2.id = flightAlternativeAircraft2.aircraft_type',
            array(
                'flightAlternativeAircraftTypeName2' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceRefuelData' => 'incomeInvoiceRefuelData'),
            'flightRefuelForm.id = incomeInvoiceRefuelData.preInvoiceRefuelId',
            array(
                'incomeInvoiceRefuelId' => 'refuelId',
                'incomeInvoiceId' => 'invoiceId',
                'incomeInvoiceParentRefuelId' => 'preInvoiceRefuelId',
                'incomeInvoiceFlightAgentId' => 'flightAgentId',
                'incomeInvoiceFlightAirOperatorId' => 'flightAirOperatorId',
                'incomeInvoiceFlightAircraftId' => 'flightAircraftId',
                'incomeInvoiceRefuelAirportId' => 'refuelAirportId',
                'incomeInvoiceRefuelDate' => 'refuelDate',
                'incomeInvoiceRefuelQuantityLtr' => 'refuelQuantityLtr',
                'incomeInvoiceRefuelQuantityOtherUnits' => 'refuelQuantityOtherUnits',
                'incomeInvoiceRefuelUnitId' => 'refuelUnitId',
                'incomeInvoiceRefuelItemPrice' => 'refuelItemPrice',
                'incomeInvoiceRefuelTax' => 'refuelTax',
                'incomeInvoiceRefuelMot' => 'refuelMot',
                'incomeInvoiceRefuelVat' => 'refuelVat',
                'incomeInvoiceRefuelDeliver' => 'refuelDeliver',
                'incomeInvoiceRefuelPrice' => 'refuelPrice',
                'incomeInvoiceRefuelPriceTotal' => 'refuelPriceTotal',
                'incomeInvoiceRefuelExchangeToUsdPriceTotal' => 'refuelExchangeToUsdPriceTotal',
            ),
            'left');

        $select->join(
            array('incomeInvoiceRefuelUnit' => 'library_unit'),
            'incomeInvoiceRefuelUnit.id = incomeInvoiceRefuelData.refuelUnitId',
            array(
                'incomeInvoiceRefuelUnitName' => 'name',
            ),
            'left');

        if ($data['dateOrderFrom'] != '' && $data['dateOrderTo'] != '') {
            $select->where->between('flight.dateOrder', $data['dateOrderFrom'], $data['dateOrderTo']);
        } else {
            if ($data['dateOrderFrom'] != '') {
                $select->where->greaterThanOrEqualTo('flight.dateOrder', $data['dateOrderFrom']);
            }

            if ($data['dateOrderTo'] != '') {
                $select->where->lessThanOrEqualTo('flight.dateOrder', $data['dateOrderTo']);
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
            $select->where->equalTo('flightRefuelForm.agentId', $data['agentId']);
        }

        if ($data['airportId'] != '') {
            $select->where->equalTo('flightRefuelForm.airportId', $data['airportId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo('flight.kontragent', $data['customerId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo('flight.airOperator', $data['airOperatorId']);
        }

        if (!empty($data['refuelsSelected'])) {
            $select->where->in('flightRefuelForm.id', $data['refuelsSelected']);
        }

        $select->order('flightRefuelForm.id ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString()); exit;
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
