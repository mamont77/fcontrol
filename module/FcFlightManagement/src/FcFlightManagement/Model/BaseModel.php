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
 * Class BaseModel
 * @package FcFlightManagement\Model
 */
class BaseModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $flightTableName = 'flightBaseHeaderForm';

    /**
     * @var string
     */
    public $legTableName = 'flightLegForm';

    /**
     * Name of the variable оставлено для совместимости с AbstractTableGateway
     *
     * @var string
     */
    public $table = '';

    /**
     * @var string
     */
    public $incomeInvoiceMainTableName = 'invoiceIncomeApServiceMain';

    /**
     * @var string
     */
    public $incomeInvoiceDataTableName = 'invoiceIncomeApServiceData';

    /**
     * @var string
     */
    public $outcomeInvoiceMainTableName = 'invoiceIncomeApServiceMain';

    /**
     * @var string
     */
    public $outcomeInvoiceDataTableName = 'invoiceIncomeApServiceData';

    /**
     * @var array
     */
    public $flightTableFieldsMap = array(
        'flightId' => 'id',
        'flightParentId' => 'parentId',
        'flightRefNumberOrder' => 'refNumberOrder',
        'flightDateOrder' => 'dateOrder',
        'flightAgentId' => 'kontragent',
        'flightAirOperatorId' => 'airOperator',
        'flightAircraftId' => 'aircraftId',
        'flightAlternativeAircraftId1' => 'alternativeAircraftId1',
        'flightAlternativeAircraftId2' => 'alternativeAircraftId2',
        'flightStatus' => 'status',
    );

    /**
     * @var array
     */
    public $legTableFieldsMap = array(
        'legId' => 'id',
        'legHeaderId' => 'headerId',
        'legDateOfFlight' => 'dateOfFlight',
        'legFlightNumberAirportId' => 'flightNumberAirportId',
        'legFlightNumberText' => 'flightNumberText',
        'legAirportDepId' => 'apDepAirportId',
        'legDepTime' => 'apDepTime',
        'legAirportArrId' => 'apArrAirportId',
        'legArrTime' => 'apArrTime',
    );

    /**
     * @var array
     */
    public $preInvoiceTableFieldsMap = array(
        'preInvoiceId' => 'id',
        'preInvoiceHeaderId' => 'headerId',
        'preInvoiceLegId' => 'legId',
        'preInvoiceAirportId' => 'airportId',
        'preInvoiceTypeOfApServiceId' => 'typeOfApServiceId',
        'preInvoiceAgentId' => 'agentId',
        'preInvoicePrice' => 'price',
        'preInvoiceCurrency' => 'currency',
        'preInvoiceExchangeRate' => 'exchangeRate',
        'preInvoicePriceTotalExchangedToUsd' => 'priceUSD',
        'preInvoiceStatus' => 'status',
    );

    /**
     * @var array
     */
    public $incomeInvoiceMainTableFieldsMap = array(
        'incomeInvoiceMainId' => 'id',
        'incomeInvoiceMainPreInvoiceId' => 'preInvoiceId',
        'incomeInvoiceMainNumber' => 'number',
        'incomeInvoiceMainDate' => 'date',
        'incomeInvoiceMainCurrency' => 'currency',
        'incomeInvoiceMainExchangeRate' => 'exchangeRate',
        'incomeInvoiceMainDateArr' => 'dateArr',
        'incomeInvoiceMainDateDep' => 'dateDep',
        'incomeInvoiceMainTypeOfService' => 'typeOfService',
        'incomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $incomeInvoiceDataTableFieldsMap = array(
        'incomeInvoiceDataId' => 'id',
        'incomeInvoiceDataInvoiceId' => 'invoiceId',
        'incomeInvoiceDataTypeOfService' => 'typeOfService',
        'incomeInvoiceDataItemPrice' => 'itemPrice',
        'incomeInvoiceDataQuantityLtr' => 'quantityLtr',
        'incomeInvoiceDataUnitId' => 'unitId',
        'incomeInvoiceDataPriceTotal' => 'priceTotal',
        'incomeInvoiceDataPriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
    );

    /**
     * @var array
     */
    public $outcomeInvoiceMainTableFieldsMap = array(
        'outcomeInvoiceMainId' => 'id',
        'outcomeInvoiceMainPreInvoiceId' => 'preInvoiceId',
        'outcomeInvoiceMainNumber' => 'number',
        'outcomeInvoiceMainDate' => 'date',
        'outcomeInvoiceMainCurrency' => 'currency',
        'outcomeInvoiceMainExchangeRate' => 'exchangeRate',
        'outcomeInvoiceMainDateArr' => 'dateArr',
        'outcomeInvoiceMainDateDep' => 'dateDep',
        'outcomeInvoiceMainTypeOfService' => 'typeOfService',
        'outcomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $outcomeInvoiceDataTableFieldsMap = array(
        'outcomeInvoiceDataId' => 'id',
        'outcomeInvoiceDataInvoiceId' => 'invoiceId',
        'outcomeInvoiceDataTypeOfService' => 'typeOfService',
        'outcomeInvoiceDataItemPrice' => 'itemPrice',
        'outcomeInvoiceDataQuantityLtr' => 'quantityLtr',
        'outcomeInvoiceDataUnitId' => 'unitId',
        'outcomeInvoiceDataPriceTotal' => 'priceTotal',
        'outcomeInvoiceDataPriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
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
}