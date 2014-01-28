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
        'legFlightNumberAirportId' => 'airOperatorId',
        'legFlightNumber' => 'flightNumber',
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
        'outcomeInvoiceMainCustomerAgreement' => 'customerAgreement',
        'outcomeInvoiceMainBankId' => 'bankId',
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
        'outcomeInvoiceMainCustomerAgreement' => 'customerAgreement',
        'outcomeInvoiceMainBankId' => 'bankId',
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
        'outcomeInvoiceDataItemPrice' => 'itemPrice',
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

    /**
     * @return array
     */
    public function getBankDetailsList()
    {
        return array(
            1 => array(
                'title' => 'SIB AED',
                'description' => 'BANK BENEFICIARY: SHARJAH ISLAMIC BANK, SHARJAH, U.A.E.<br />
                    SWIFT: NBSHAEAS<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.<br />
                    IBAN ACCOUNT NO. (AED): AE39 0410 0000 32508002001',
            ),
            2 => array(
                'title' => 'SIB USD',
                'description' => 'BANK BENEFICIARY: SHARJAH ISLAMIC BANK, SHARJAH, U.A.E.<br />
                    SWIFT: NBSHAEAS<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.<br />
                    IBAN ACCOUNT NO. (USD): AE12 0410 0000 32508002002<br />
                    <br />
                    Correspondent bank: Deutsch Bank Trust Company, NEW YORK, USA <br />
                    Swift: BKTRUS33<br />
                    ACC: 04-169575',
            ),
            3 => array(
                'title' => 'SIB EUR',
                'description' => 'BANK BENEFICIARY: SHARJAH ISLAMIC BANK, SHARJAH, U.A.E.<br />
                    SWIFT: NBSHAEAS<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.<br />
                    IBAN ACCOUNT NO. (EUR): AE82 0410 0000 32508002003',
            ),
            4 => array(
                'title' => 'MASH AED',
                'description' => 'BENEFICIARY BANK: MASHREQBANK PSC, SHARJAH K.A.A. Branch, U.A.E.<br />
                    IBAN BENEFICIARY ACCOUNT No.(AED): AE570330000010496305788<br />
                    SWIFT CODE: BOMLAEAD<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.',
            ),
            5 => array(
                'title' => 'MASH USD',
                'description' => 'CORRESPONDENT BANK: MASHREQBANK, NEW YORK,<br />
                    SWIFT: MSHQUS33<br />
                    CORR. ACCOUNT: 0123452<br />
                    <br />
                    BENEFICIARY BANK: MASHREQBANK PSC, SHARJAH K.A.A. Branch, U.A.E.<br />
                    IBAN BENEFICIARY ACCOUNT No.(USD): AE100330000010448962098<br />
                    SWIFT CODE: BOMLAEAD<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.',
            ),
            6 => array(
                'title' => 'MASH EUR',
                'description' => 'CORR. BANK: MASHREQ BANK, LONDON, UK<br />
                    CORR. SWIFT: MSHQGB2L<br />
                    <br />
                    BANK BENEFICIARY: MASHREQBANK PSC, SHARJAH K.A.A. Branch, U.A.E.<br />
                    SWIFT: BOMLAEAD<br />
                    IBAN ACCOUNT (EUR): AE43 0330 0000 1045 5080 264<br />
                    BENEFICIARY: CARGO AIR CHARTERING F.Z.E.',
            ),
        );
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getBankDetailById($id)
    {
        $banks = $this->getBankDetailsList();
        $row = $banks[$id];

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }
}
