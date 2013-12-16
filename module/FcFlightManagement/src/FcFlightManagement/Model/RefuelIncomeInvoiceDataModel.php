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
 * Class RefuelIncomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class RefuelIncomeInvoiceDataModel extends RefuelIncomeInvoiceMainModel
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomeRefuelData';

    /**
     * @var array
     */
    protected $_tableFields = array(
        'refuelId' => 'refuelId',
        'invoiceId' => 'invoiceId',
        'preInvoiceRefuelId' => 'preInvoiceRefuelId',
        'flightAgentId' => 'flightAgentId',
        'flightAirOperatorId' => 'flightAirOperatorId',
        'flightAirOperatorNumber' => 'flightAirOperatorNumber',
        'flightAircraftId' => 'flightAircraftId',
        'refuelAirportId' => 'refuelAirportId',
        'refuelDate' => 'refuelDate',
        'refuelQuantityLtr' => 'refuelQuantityLtr',
        'refuelQuantityOtherUnits' => 'refuelQuantityOtherUnits',
        'refuelUnitId' => 'refuelUnitId',
        'refuelItemPrice' => 'refuelItemPrice',
        'refuelTax' => 'refuelTax',
        'refuelMot' => 'refuelMot',
        'refuelVat' => 'refuelVat',
        'refuelDeliver' => 'refuelDeliver',
        'refuelPrice' => 'refuelPrice',
        'refuelPriceTotal' => 'refuelPriceTotal',
        'refuelExchangeToUsdPriceTotal' => 'refuelExchangeToUsdPriceTotal',
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
     * @param $data
     * @return int
     */
    public function add($data)
    {
//        \Zend\Debug\Debug::dump($data);
        $refuelDate = \DateTime::createFromFormat('d-m-Y', $data['refuelDate']);
        $data['refuelDate'] = $refuelDate->setTime(0, 0, 0)->getTimestamp();

        $data = array(
            'invoiceId' => (int)$data['invoiceId'],
            'preInvoiceRefuelId' => (int)$data['preInvoiceRefuelId'],
            'flightAgentId' => (int)$data['flightAgentId'],
            'flightAirOperatorId' => (int)$data['flightAirOperatorId'],
            'flightAirOperatorNumber' => (string)$data['flightAirOperatorNumber'],
            'flightAircraftId' => (int)$data['flightAircraftId'],
            'refuelAirportId' => (int)$data['refuelAirportId'],
            'refuelDate' => (int)$data['refuelDate'],
            'refuelQuantityLtr' => (string)$data['refuelQuantityLtr'],
            'refuelQuantityOtherUnits' => (string)$data['refuelQuantityOtherUnits'],
            'refuelUnitId' => (int)$data['refuelUnitId'],
            'refuelItemPrice' => (string)$data['refuelItemPrice'],
            'refuelTax' => (string)$data['refuelTax'],
            'refuelMot' => (string)$data['refuelMot'],
            'refuelVat' => (string)$data['refuelVat'],
            'refuelDeliver' => (string)$data['refuelDeliver'],
            'refuelPrice' => (string)$data['refuelPrice'],
            'refuelPriceTotal' => (string)$data['refuelPriceTotal'],
            'refuelExchangeToUsdPriceTotal' => (string)$data['refuelExchangeToUsdPriceTotal'],
        );

        $this->insert($data);

        return $this->getLastInsertValue();
    }

    public function getByInvoiceId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->_tableFields);

        $select->join(array('preInvoiceRefuel' => 'flightRefuelForm'),
            'preInvoiceRefuel.id = ' . $this->table . '.preInvoiceRefuelId',
            array(
                'preInvoiceRefuelId' => 'id',
                'preInvoiceHeaderId' => 'headerId',
                'preInvoiceAgentId' => 'agentId',
                'preInvoiceLegId' => 'legId',
                'preInvoiceAirportId' => 'airportId',
                'preInvoiceQuantityLtr' => 'quantityLtr',
                'preInvoiceQuantityOtherUnits' => 'quantityOtherUnits',
                'preInvoiceUnitId' => 'unitId',
                'preInvoicePriceUsd' => 'priceUsd',
                'preInvoiceTotalPriceUsd' => 'totalPriceUsd',
                'preInvoiceDate' => 'date',
                'preInvoiceStatus' => 'status',
            ), 'left');

        $select->join(array('preInvoiceHeader' => 'flightBaseHeaderForm'),
            'preInvoiceHeader.id = preInvoiceRefuel.headerId',
            array(
                'preInvoiceHeaderRefNumberOrder' => 'refNumberOrder',
                'preInvoiceHeaderDateOrder' => 'dateOrder',
                'preInvoiceHeaderAgentId' => 'kontragent',
                'preInvoiceHeaderAirOperatorId' => 'airOperator',
                'preInvoiceHeaderAircraftId' => 'aircraftId',
                'preInvoiceHeaderAlternativeAircraftId1' => 'alternativeAircraftId1',
                'preInvoiceHeaderAlternativeAircraftId2' => 'alternativeAircraftId2',
                'preInvoiceHeaderStatus' => 'status',
            ), 'left');

        $select->join(
            array('preInvoiceHeaderAgent' => 'library_kontragent'),
            'preInvoiceHeaderAgent.id = ' . $this->table . '.flightAgentId',
            array(
                'preInvoiceHeaderAgentName' => 'name',
                'preInvoiceHeaderAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('preInvoiceHeaderAirOperator' => 'library_air_operator'),
            'preInvoiceHeaderAirOperator.id = ' . $this->table . '.flightAirOperatorId',
            array(
                'preInvoiceHeaderAirOperatorName' => 'name',
                'preInvoiceHeaderOperatorShortName' => 'short_name',
                'preInvoiceHeaderAirOperatorICAO' => 'code_icao',
                'preInvoiceHeaderAirOperatorIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceHeaderAircraft' => 'library_aircraft'),
            'preInvoiceHeaderAircraft.id = ' . $this->table . '.flightAircraftId',
            array(
                'preInvoiceHeaderAircraftTypeId' => 'aircraft_type',
                'preInvoiceHeaderAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('preInvoiceHeaderAircraftType' => 'library_aircraft_type'),
            'preInvoiceHeaderAircraftType.id = preInvoiceHeaderAircraft.aircraft_type',
            array(
                'preInvoiceHeaderAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('preInvoiceRefuelAirport' => 'library_airport'),
            'preInvoiceRefuelAirport.id = ' . $this->table . '.refuelAirportId',
            array(
                'preInvoiceRefuelAirportName' => 'name',
                'preInvoiceRefuelAirportShortName' => 'short_name',
                'preInvoiceRefuelAirportICAO' => 'code_icao',
                'preInvoiceRefuelAirportIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceHeaderUnit' => 'library_unit'),
            'preInvoiceHeaderUnit.id = ' . $this->table . '.refuelUnitId',
            array(
                'preInvoiceHeaderUnitName' => 'name',
            ),
            'left');

        $select->where(array('invoiceId' => $id));
        $select->order(array('refuelId ' . $select::ORDER_ASCENDING));
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
