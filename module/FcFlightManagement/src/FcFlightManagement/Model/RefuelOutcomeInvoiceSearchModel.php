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
        'incomeInvoiceAirOperatorNumber' => 'flightAirOperatorNumber',
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
        'outcomeInvoiceAirOperatorNumber' => 'airOperatorNumber',
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

        $select->join(
            array('incomeInvoiceAgent' => 'library_kontragent'),
            $this->table . '.flightAgentId = incomeInvoiceAgent.id',
            array(
                'incomeInvoiceAgentName' => 'name',
                'incomeInvoiceAgentShortName' => 'short_name',
                'incomeInvoiceAgentAgreement' => 'agreement',
                'incomeInvoiceAgentTermOfPayment' => 'termOfPayment',
            ),
            'left');

        $select->join(
            array('incomeInvoiceAirOperator' => 'library_air_operator'),
            $this->table . '.flightAirOperatorId = incomeInvoiceAirOperator.id',
            array(
                'incomeInvoiceAirOperatorName' => 'name',
                'incomeInvoiceAirOperatorShortName' => 'short_name',
                'incomeInvoiceAirOperatorICAO' => 'code_icao',
                'incomeInvoiceAirOperatorIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('incomeInvoiceAircraft' => 'library_aircraft'),
            $this->table . '.flightAircraftId = incomeInvoiceAircraft.id',
            array(
                'incomeInvoiceAircraftTypeId' => 'aircraft_type',
                'incomeInvoiceAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('incomeInvoiceAircraftType' => 'library_aircraft_type'),
            'incomeInvoiceAircraft.aircraft_type = incomeInvoiceAircraftType.id',
            array(
                'incomeInvoiceAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceAirportDep' => 'library_airport'),
            $this->table . '.refuelAirportId = incomeInvoiceAirportDep.id',
            array(
                'incomeInvoiceAirportDepName' => 'name',
                'incomeInvoiceAirportDepShortName' => 'short_name',
                'incomeInvoiceAirportDepICAO' => 'code_icao',
                'incomeInvoiceAirportDepIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('incomeInvoiceSupplier' => 'library_kontragent'),
            'invoiceIncomeRefuelMain.invoiceRefuelSupplierId = incomeInvoiceSupplier.id',
            array(
                'incomeInvoiceSupplierName' => 'name',
                'incomeInvoiceSupplierShortName' => 'short_name',
                'incomeInvoiceSupplierAgreement' => 'agreement',
                'incomeInvoiceSupplierTermOfPayment' => 'termOfPayment',
            ),
            'left');

        $select->join(
            array('incomeInvoiceUnit' => 'library_unit'),
            $this->table . '.refuelUnitId = incomeInvoiceUnit.id',
            array(
                'incomeInvoiceUnitName' => 'name',
            ),
            'left');

        $select->join(
            array('outcomeInvoiceUnit' => 'library_unit'),
            'invoiceOutcomeRefuelData.unitId = outcomeInvoiceUnit.id',
            array(
                'outcomeInvoiceUnitName' => 'name',
            ),
            'left');

        $select->where->equalTo('flight.isYoungest', 1);

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
            $select->where->equalTo('invoiceIncomeRefuelMain.invoiceRefuelSupplierId', $data['agentId']);
        }

        if ($data['airportId'] != '') {
            $select->where->equalTo($this->table . '.refuelAirportId', $data['airportId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo($this->table . '.flightAgentId', $data['customerId']);
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
