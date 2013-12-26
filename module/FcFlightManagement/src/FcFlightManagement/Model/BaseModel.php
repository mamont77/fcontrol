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
    public $apServicePreInvoiceMainTableName = 'flightApServiceForm';

    /**
     * @var string
     */
    public $apServiceIncomeInvoiceMainTableName = 'invoiceIncomeApServiceMain';

    /**
     * @var string
     */
    public $apServiceIncomeInvoiceDataTableName = 'invoiceIncomeApServiceData';

    /**
     * @var string
     */
    public $apServiceOutcomeInvoiceMainTableName = 'invoiceOutcomeApServiceMain';

    /**
     * @var string
     */
    public $apServiceOutcomeInvoiceDataTableName = 'invoiceOutcomeApServiceData';

    /**
     * @var string
     */
    public $permissionPreInvoiceMainTableName = 'flightPermissionForm';

    /**
     * @var string
     */
    public $permissionIncomeInvoiceMainTableName = 'invoiceIncomePermissionMain';

    /**
     * @var string
     */
    public $permissionIncomeInvoiceDataTableName = 'invoiceIncomePermissionData';

    /**
     * @var string
     */
    public $permissionOutcomeInvoiceMainTableName = 'invoiceOutcomePermissionMain';

    /**
     * @var string
     */
    public $permissionOutcomeInvoiceDataTableName = 'invoiceOutcomePermissionData';

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
    public $apServicePreInvoiceTableFieldsMap = array(
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
    public $apServiceIncomeInvoiceMainTableFieldsMap = array(
        'incomeInvoiceMainId' => 'id',
        'incomeInvoiceMainPreInvoiceId' => 'preInvoiceId',
        'incomeInvoiceMainNumber' => 'number',
        'incomeInvoiceMainDate' => 'date',
        'incomeInvoiceMainCurrency' => 'currency',
        'incomeInvoiceMainExchangeRate' => 'exchangeRate',
        'incomeInvoiceMainDateArr' => 'dateArr',
        'incomeInvoiceMainDateDep' => 'dateDep',
        'incomeInvoiceFlightNumber' => 'flightNumber',
        'incomeInvoiceMainTypeOfServiceId' => 'typeOfServiceId',
        'incomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $apServiceIncomeInvoiceDataTableFieldsMap = array(
        'incomeInvoiceDataId' => 'id',
        'incomeInvoiceDataInvoiceId' => 'invoiceId',
        'incomeInvoiceDataTypeOfServiceId' => 'typeOfServiceId',
        'incomeInvoiceDataItemPrice' => 'itemPrice',
        'incomeInvoiceDataQuantity' => 'quantity',
        'incomeInvoiceDataUnitId' => 'unitId',
        'incomeInvoiceDataPriceTotal' => 'priceTotal',
        'incomeInvoiceDataPriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
    );

    /**
     * @var array
     */
    public $apServiceOutcomeInvoiceMainTableFieldsMap = array(
        'outcomeInvoiceMainId' => 'id',
        'outcomeInvoiceMainIncomeInvoiceId' => 'incomeInvoiceId',
        'outcomeInvoiceMainNumber' => 'number',
        'outcomeInvoiceMainDate' => 'date',
        'outcomeInvoiceMainCurrency' => 'currency',
        'outcomeInvoiceMainExchangeRate' => 'exchangeRate',
        'outcomeInvoiceMainDateArr' => 'dateArr',
        'outcomeInvoiceMainDateDep' => 'dateDep',
        'outcomeInvoiceFlightNumber' => 'flightNumber',
        'outcomeInvoiceMainTypeOfServiceId' => 'typeOfServiceId',
        'disbursement' => 'disbursement',
        'disbursementTotal' => 'disbursementTotal',
        'disbursementTotalExchangedToUsd' => 'disbursementTotalExchangedToUsd',
        'outcomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $apServiceOutcomeInvoiceDataTableFieldsMap = array(
        'outcomeInvoiceDataId' => 'id',
        'outcomeInvoiceDataInvoiceId' => 'invoiceId',
        'outcomeInvoiceDataTypeOfService' => 'typeOfServiceId',
        'outcomeInvoiceDataItemPrice' => 'itemPrice',
        'outcomeInvoiceDataQuantity' => 'quantity',
        'outcomeInvoiceDataUnitId' => 'unitId',
        'outcomeInvoiceDataPriceTotal' => 'priceTotal',
        'outcomeInvoiceDataPriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
        'outcomeInvoiceDataIsAdditionalInfo' => 'isAdditionalInfo',
    );

    /**
     * @var array
     */
    public $permissionPreInvoiceTableFieldsMap = array(
        'preInvoiceId' => 'id',
        'preInvoiceHeaderId' => 'headerId',
        'preInvoiceAgentId' => 'agentId',
        'preInvoiceLegId' => 'legId',
        'preInvoiceCountryId' => 'countryId',
        'preInvoiceTypeOfPermission' => 'typeOfPermission',
        'preInvoiceRequestTime' => 'requestTime',
        'preInvoicePermission' => 'permission',
        'preInvoiceComment' => 'comment',
        'preInvoiceStatus' => 'status',
    );

    /**
     * @var array
     */
    public $permissionIncomeInvoiceMainTableFieldsMap = array(
        'incomeInvoiceMainId' => 'id',
        'incomeInvoiceMainNumber' => 'number',
        'incomeInvoiceMainDate' => 'date',
        'incomeInvoiceMainCurrency' => 'currency',
        'incomeInvoiceMainExchangeRate' => 'exchangeRate',
        'incomeInvoiceMainAgentId' => 'agentId',
        'incomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $permissionIncomeInvoiceDataTableFieldsMap = array(
        'incomeInvoiceDataId' => 'id',
        'incomeInvoiceDataInvoiceId' => 'invoiceId',
        'incomeInvoiceDataPreInvoiceId' => 'preInvoiceId',
        'incomeInvoiceDataFlightNumber' => 'flightNumber',
        'incomeInvoiceDataAircraftId' => 'aircraftId',
        'incomeInvoiceDataDateDep' => 'dateDep',
        'incomeInvoiceDataDateArr' => 'dateArr',
        'incomeInvoiceDataTypeOfPermission' => 'typeOfPermission',
        'incomeInvoiceDataItemPrice' => 'itemPrice',
        'incomeInvoiceDataQuantity' => 'quantity',
        'incomeInvoiceDataUnitId' => 'unitId',
        'incomeInvoiceDataPriceTotal' => 'priceTotal',
        'incomeInvoiceDataPriceTotalExchangedToUsd' => 'priceTotalExchangedToUsd',
    );

    /**
     * @var array
     */
    public $permissionOutcomeInvoiceMainTableFieldsMap = array(
        'outcomeInvoiceMainId' => 'id',
        'outcomeInvoiceMainNumber' => 'number',
        'outcomeInvoiceMainDate' => 'date',
        'outcomeInvoiceMainCurrency' => 'currency',
        'outcomeInvoiceMainExchangeRate' => 'exchangeRate',
        'outcomeInvoiceMainCustomerId' => 'customerId',
        'outcomeInvoiceMainStatus' => 'status',
    );

    /**
     * @var array
     */
    public $permissionOutcomeInvoiceDataTableFieldsMap = array(
        'outcomeInvoiceDataId' => 'id',
        'incomeInvoiceDataInvoiceId' => 'invoiceId',
        'outcomeInvoiceDataIncomeInvoiceId' => 'incomeInvoiceId',
        'outcomeInvoiceDataFlightNumber' => 'flightNumber',
        'outcomeInvoiceDataAircraftId' => 'aircraftId',
        'outcomeInvoiceDataDateDep' => 'dateDep',
        'outcomeInvoiceDataDateArr' => 'dateArr',
        'outcomeInvoiceDataTypeOfPermission' => 'typeOfPermission',
        'incomeInvoiceDataItemPrice' => 'itemPrice',
        'outcomeInvoiceDataQuantity' => 'quantity',
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
