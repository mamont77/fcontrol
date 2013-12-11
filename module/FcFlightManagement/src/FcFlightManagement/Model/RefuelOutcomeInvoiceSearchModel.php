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
 * Class RefuelOutcomeInvoiceSearchModel
 * @package FcFlightManagement\Model
 */
class RefuelOutcomeInvoiceSearchModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomeRefuelData';

    /**
     * @var array
     */
    protected $_tableFieldsIncomeInvoice = array(
        'incomeInvoiceRefuelId' => 'refuelId',
        'incomeInvoiceInvoiceId' => 'invoiceId',
        'incomeInvoicePreInvoiceRefuelId' => 'preInvoiceRefuelId', // необходим что бы получить ORD, status
        'incomeInvoiceAgentId' => 'flightAgentId',
        'incomeInvoiceAirOperatorId' => 'flightAirOperatorId',
        'incomeInvoiceAircraftId' => 'flightAircraftId',
        'incomeInvoiceAirportId' => 'refuelAirportId',
        'incomeInvoiceDate' => 'refuelDate',
        'incomeInvoiceQuantityLtr' => 'refuelQuantityLtr',
        'incomeInvoiceQuantityOtherUnits' => 'refuelQuantityOtherUnits',
        'incomeInvoiceUnitId' => 'refuelUnitId',
        'incomeInvoiceItemPrice' => 'refuelItemPrice',
        'incomeInvoiceTax' => 'refuelTax',
        'incomeInvoiceMot' => 'refuelMot',
        'incomeInvoiceVat' => 'refuelVat',
        'incomeInvoiceDeliver' => 'refuelDeliver',
        'incomeInvoicePrice' => 'refuelPrice',
        'incomeInvoicePriceTotal' => 'refuelPriceTotal',
        'incomeInvoicePriceTotalExchangedToUsd' => 'refuelExchangeToUsdPriceTotal',
    );

    /**
     * @var array
     */
    protected $_tableFieldsOutcomeInvoice = array(
        'outcomeInvoiceRefuelId' => 'refuelId',
        'outcomeInvoiceInvoiceId' => 'invoiceId',
        'outcomeInvoicePreInvoiceRefuelId' => 'incomeInvoiceRefuelId',
        'outcomeInvoiceSupplierId' => 'supplierId',
        'outcomeInvoiceAirOperatorId' => 'airOperatorId',
        'outcomeInvoiceAircraftId' => 'aircraftId',
        'outcomeInvoiceAirportId' => 'airportDepId',
        'outcomeInvoiceDate' => 'date',
        'outcomeInvoiceQuantityLtr' => 'quantityLtr',
        'outcomeInvoiceQuantityOtherUnits' => 'quantityOtherUnits',
        'outcomeInvoiceUnitId' => 'unitId',
        'outcomeInvoiceItemPrice' => 'itemPrice',
        'outcomeInvoiceTax' => 'tax',
        'outcomeInvoiceMot' => 'mot',
        'outcomeInvoiceVat' => 'vat',
        'outcomeInvoiceDeliver' => 'deliver',
        'outcomeInvoicePrice' => 'price',
        'outcomeInvoicePriceTotal' => 'priceTotal',
        'outcomeInvoicePriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
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

        $select->columns($this->_tableFieldsIncomeInvoice);

        $select->join(
            array('invoiceIncomeRefuelMain' => 'invoiceIncomeRefuelMain'),
            $this->table . '.invoiceId = invoiceIncomeRefuelMain.invoiceId',
            array(
                'incomeInvoiceMainId' => 'invoiceId',
                'incomeInvoiceMainNumber' => 'invoiceNumber',
                'incomeInvoiceMainDate' => 'invoiceDate',
                'incomeInvoiceMainCurrency' => 'invoiceCurrency',
                'incomeInvoiceMainExchangeRate' => 'invoiceExchangeRate',
                'incomeInvoiceMainRefuelSupplierId' => 'invoiceRefuelSupplierId',
                'incomeInvoiceMainStatus' => 'invoiceStatus',
            ),
            'left');

        $select->join(
            array('invoiceOutcomeRefuelData' => 'invoiceOutcomeRefuelData'),
            $this->table . '.refuelId = invoiceOutcomeRefuelData.incomeInvoiceRefuelId',
            $this->_tableFieldsOutcomeInvoice,
            'left');

        $select->join(
            array('invoiceOutcomeRefuelMain' => 'invoiceOutcomeRefuelMain'),
            'invoiceOutcomeRefuelData.invoiceId = invoiceOutcomeRefuelMain.invoiceId',
            array(
                'outcomeInvoiceMainId' => 'invoiceId',
                'outcomeInvoiceMainNumber' => 'invoiceNumber',
                'outcomeInvoiceMainDate' => 'invoiceDate',
                'outcomeInvoiceMainCurrency' => 'invoiceCurrency',
                'outcomeInvoiceMainExchangeRate' => 'invoiceExchangeRate',
                'outcomeInvoiceMainRefuelSupplierId' => 'invoiceCustomerId',
                'outcomeInvoiceMainStatus' => 'invoiceStatus',
            ),
            'left');

        $select->join(
            array('preInvoiceRefuel' => 'flightRefuelForm'),
            $this->table . '.preInvoiceRefuelId = preInvoiceRefuel.id',
            array(
                'preInvoiceFlightId' => 'headerId',
                'preInvoiceRefuelStatus' => 'status',
            ),
            'left');

        $select->join(
            array('flight' => 'flightBaseHeaderForm'),
            'preInvoiceRefuel.headerId = flight.id',
            array(
                'preInvoiceFlightRefNumberOrder' => 'refNumberOrder',
                'preInvoiceFlightStatus' => 'status',
            ),
            'left');

//
//        $select->join(
//            array('refuelAgent' => 'library_kontragent'),
//            'refuelAgent.id = flightRefuelForm.agentId',
//            array(
//                'refuelAgentName' => 'name',
//                'refuelAgentShortName' => 'short_name',
//            ),
//            'left');
//
//        $select->join(
//            array('refuelAirport' => 'library_airport'),
//            'refuelAirport.id = flightRefuelForm.airportId',
//            array(
//                'refuelAirportName' => 'name',
//                'refuelAirportShortName' => 'short_name',
//                'refuelAirportICAO' => 'code_icao',
//                'refuelAirportIATA' => 'code_iata',
//            ),
//            'left');
//
//        $select->join(
//            array('refuelUnit' => 'library_unit'),
//            'refuelUnit.id = flightRefuelForm.unitId',
//            array(
//                'refuelUnitName' => 'name',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAgent' => 'library_kontragent'),
//            'flightAgent.id = flight.kontragent',
//            array(
//                'flightAgentName' => 'name',
//                'flightAgentShortName' => 'short_name',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAirOperator' => 'library_air_operator'),
//            'flightAirOperator.id = flight.airOperator',
//            array(
//                'flightAirOperatorName' => 'name',
//                'flightAirOperatorShortName' => 'short_name',
//                'flightAirOperatorICAO' => 'code_icao',
//                'flightAirOperatorIATA' => 'code_iata',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAircraft' => 'library_aircraft'),
//            'flightAircraft.id = flight.aircraftId',
//            array(
//                'flightAircraftTypeId' => 'aircraft_type',
//                'flightAircraftName' => 'reg_number',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAircraftType' => 'library_aircraft_type'),
//            'flightAircraftType.id = flightAircraft.aircraft_type',
//            array(
//                'flightAircraftTypeName' => 'name',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAlternativeAircraft1' => 'library_aircraft'),
//            'flightAlternativeAircraft1.id = flight.alternativeAircraftId1',
//            array(
//                'flightAlternativeAircraftTypeId1' => 'aircraft_type',
//                'flightAlternativeAircraftName1' => 'reg_number',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAlternativeTypeAircraft1' => 'library_aircraft_type'),
//            'flightAlternativeTypeAircraft1.id = flightAlternativeAircraft1.aircraft_type',
//            array(
//                'flightAlternativeAircraftTypeName1' => 'name',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAlternativeAircraft2' => 'library_aircraft'),
//            'flightAlternativeAircraft2.id = flight.alternativeAircraftId2',
//            array(
//                'flightAlternativeAircraftTypeId2' => 'aircraft_type',
//                'flightAlternativeAircraftName2' => 'reg_number',
//            ),
//            'left');
//
//        $select->join(
//            array('flightAlternativeTypeAircraft2' => 'library_aircraft_type'),
//            'flightAlternativeTypeAircraft2.id = flightAlternativeAircraft2.aircraft_type',
//            array(
//                'flightAlternativeAircraftTypeName2' => 'name',
//            ),
//            'left');
//
//        $select->join(
//            array('invoiceIncomeRefuelData' => 'invoiceIncomeRefuelData'),
//            'flightRefuelForm.id = invoiceIncomeRefuelData.preInvoiceRefuelId',
//            array(
//                'incomeInvoiceRefuelId' => 'refuelId',
//                'incomeInvoiceId' => 'invoiceId',
//                'incomeInvoiceParentRefuelId' => 'preInvoiceRefuelId',
//                'incomeInvoiceFlightAgentId' => 'flightAgentId',
//                'incomeInvoiceFlightAirOperatorId' => 'flightAirOperatorId',
//                'incomeInvoiceFlightAircraftId' => 'flightAircraftId',
//                'incomeInvoiceRefuelAirportId' => 'refuelAirportId',
//                'incomeInvoiceRefuelDate' => 'refuelDate',
//                'incomeInvoiceRefuelQuantityLtr' => 'refuelQuantityLtr',
//                'incomeInvoiceRefuelQuantityOtherUnits' => 'refuelQuantityOtherUnits',
//                'incomeInvoiceRefuelUnitId' => 'refuelUnitId',
//                'incomeInvoiceRefuelItemPrice' => 'refuelItemPrice',
//                'incomeInvoiceRefuelTax' => 'refuelTax',
//                'incomeInvoiceRefuelMot' => 'refuelMot',
//                'incomeInvoiceRefuelVat' => 'refuelVat',
//                'incomeInvoiceRefuelDeliver' => 'refuelDeliver',
//                'incomeInvoiceRefuelPrice' => 'refuelPrice',
//                'incomeInvoiceRefuelPriceTotal' => 'refuelPriceTotal',
//                'incomeInvoiceRefuelExchangeToUsdPriceTotal' => 'refuelExchangeToUsdPriceTotal',
//            ),
//            'left');
//
//        $select->join(
//            array('incomeInvoiceRefuelUnit' => 'library_unit'),
//            'incomeInvoiceRefuelUnit.id = invoiceIncomeRefuelData.refuelUnitId',
//            array(
//                'incomeInvoiceRefuelUnitName' => 'name',
//            ),
//            'left');

        if ($data['dateFrom'] != '' && $data['dateTo'] != '') {
            $select->where->between($this->table . '.refuelDate', $data['dateFrom'], $data['dateTo']);
        } else {
            if ($data['dateFrom'] != '') {
                $select->where->greaterThanOrEqualTo($this->table . '.refuelDate', $data['dateFrom']);
            }

            if ($data['dateTo'] != '') {
                $select->where->lessThanOrEqualTo($this->table . '.refuelDate', $data['dateTo']);
            }
        }

        if ($data['aircraftId'] != '') {
            $select->where->equalTo($this->table . '.flightAircraftId', $data['aircraftId']);
        }

        if ($data['agentId'] != '') {
            $select->where->equalTo($this->table . '.flightAgentId', $data['agentId']);
        }

        if ($data['airportId'] != '') {
            $select->where->equalTo($this->table . '.refuelAirportId', $data['airportId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo('invoiceIncomeRefuelMain.invoiceRefuelSupplierId', $data['customerId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo($this->table . '.flightAirOperatorId', $data['airOperatorId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo($this->table . '.flightAirOperatorId', $data['airOperatorId']);
        }

        if ($data['typeOfInvoice'] == 'both') {
            $select->where->isNotNull('invoiceOutcomeRefuelData.refuelId');
        }

        if (!empty($data['refuelsSelected'])) {
            $select->where->in($this->table . '.refuelId', $data['refuelsSelected']);
        }

        $select->order('invoiceIncomeRefuelData.refuelId ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString()); exit;
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
