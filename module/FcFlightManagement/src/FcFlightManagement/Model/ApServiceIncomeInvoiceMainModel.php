<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use FcFlight\Form\BaseForm;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class ApServiceIncomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class ApServiceIncomeInvoiceMainModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomeApServiceMain';

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
        $data['date'] = \DateTime::createFromFormat('d-m-Y', $data['date'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateArr'] = \DateTime::createFromFormat('d-m-Y', $data['dateArr'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateDep'] = \DateTime::createFromFormat('d-m-Y', $data['dateDep'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->apServiceIncomeInvoiceMainTableFieldsMap);

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

    public function get($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns($this->apServiceIncomeInvoiceMainTableFieldsMap);

        $select->join(
            array('preInvoice' => 'flightApServiceForm'),
            $this->table . '.preInvoiceId = preInvoice.id',
            $this->apServicePreInvoiceTableFieldsMap,
            'left');

        $select->join(
            array('flight' => $this->flightTableName),
            'preInvoice.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left');

        $select->join(
            array('leg' => $this->legTableName),
            'preInvoice.legId = leg.id',
            $this->legTableFieldsMap,
            'left');

        $select->join(
            array('preInvoiceAgent' => 'library_kontragent'),
            'preInvoice.agentId = preInvoiceAgent.id',
            array(
                'preInvoiceAgentName' => 'name',
                'preInvoiceAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceMainTypeOfService' => 'library_type_of_ap_service'),
            $this->table . '.typeOfServiceId = incomeInvoiceMainTypeOfService.id',
            array(
                'incomeInvoiceMainTypeOfServiceName' => 'name',
            ),
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
            array('incomeInvoiceMainAircraft' => 'library_aircraft'),
            $this->table . '.aircraftId = incomeInvoiceMainAircraft.id',
            array(
                'incomeInvoiceMainAircraftTypeId' => 'aircraft_type',
                'incomeInvoiceMainAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('incomeInvoiceMainAircraftType' => 'library_aircraft_type'),
            'incomeInvoiceMainAircraft.aircraft_type = incomeInvoiceMainAircraftType.id',
            array(
                'incomeInvoiceMainAircraftTypeName' => 'name',
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

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

//        $row->invoiceDate = date('d-m-Y', $row->invoiceDate);

        return $row;
    }
}
