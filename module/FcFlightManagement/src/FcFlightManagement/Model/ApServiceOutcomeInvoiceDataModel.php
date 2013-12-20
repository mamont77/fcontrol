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
 * Class ApServiceOutcomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class ApServiceOutcomeInvoiceDataModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomeApServiceData';

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
     * @param bool $isAdditionalInfo
     * @return int
     */
    public function add($data, $isAdditionalInfo = false)
    {
        $data['isAdditionalInfo'] = ($isAdditionalInfo) ? 1 : 0;
        $fields = array_flip($this->apServiceOutcomeInvoiceDataTableFieldsMap);

        foreach ($fields as $key => &$field) {
            if (isset($data[$key])) {
                $field = $data[$key];
            } else {
                unset($fields[$key]);
            }
        }

//        \Zend\Debug\Debug::dump($fields);exit;

        $this->insert($fields);

        return $this->getLastInsertValue();
    }

    public function getByInvoiceId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->_tableFields);

        $select->join(array('invoiceIncomeRefuelData' => 'invoiceIncomeRefuelData'),
            'invoiceIncomeRefuelData.refuelId = ' . $this->table . '.incomeInvoiceRefuelId',
            array(
                'preInvoiceInvoiceId' => 'invoiceId',
                'preInvoiceRefuelId' => 'preInvoiceRefuelId',
            ), 'left');


        $select->join(array('invoiceIncomeRefuelMain' => 'invoiceIncomeRefuelMain'),
            'invoiceIncomeRefuelMain.invoiceId = invoiceIncomeRefuelData.invoiceId',
            array(
                'invoiceIncomeNumber' => 'invoiceNumber',
                'invoiceIncomeStatus' => 'invoiceStatus',
            ), 'left');

        $select->join(array('preInvoiceRefuel' => 'flightRefuelForm'),
            'preInvoiceRefuel.id = ' . 'invoiceIncomeRefuelData.preInvoiceRefuelId',
            array(
                'preInvoiceHeaderId' => 'headerId',
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
            array('supplier' => 'library_kontragent'),
            'supplier.id = ' . $this->table . '.supplierId',
            array(
                'supplierName' => 'name',
                'supplierShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('airOperator' => 'library_air_operator'),
            'airOperator.id = ' . $this->table . '.airOperatorId',
            array(
                'airOperatorName' => 'name',
                'airOperatorShortName' => 'short_name',
                'airOperatorICAO' => 'code_icao',
                'airOperatorIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('aircraft' => 'library_aircraft'),
            'aircraft.id = ' . $this->table . '.aircraftId',
            array(
                'aircraftTypeId' => 'aircraft_type',
                'aircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('aircraftType' => 'library_aircraft_type'),
            'aircraftType.id = aircraft.aircraft_type',
            array(
                'aircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('airportDep' => 'library_airport'),
            'airportDep.id = ' . $this->table . '.airportDepId',
            array(
                'airportDepName' => 'name',
                'airportDepShortName' => 'short_name',
                'airportDepICAO' => 'code_icao',
                'airportDepIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('unit' => 'library_unit'),
            'unit.id = ' . $this->table . '.unitId',
            array(
                'unitName' => 'name',
            ),
            'left');

        $select->where(array($this->table . '.invoiceId' => $id));
        $select->order(array($this->table . '.refuelId ' . $select::ORDER_ASCENDING));
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
