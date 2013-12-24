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
 * Class PermissionOutcomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class PermissionOutcomeInvoiceMainModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomePermissionMain';

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

        $fields = array_flip($this->permissionOutcomeInvoiceMainTableFieldsMap);

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

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function get($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns($this->permissionOutcomeInvoiceMainTableFieldsMap);

        $select->join(array('outcomeInvoiceMainTypeOfService' => 'library_type_of_ap_service'),
            $this->table . '.typeOfServiceId = outcomeInvoiceMainTypeOfService.id',
            array('outcomeInvoiceMainTypeOfServiceName' => 'name'),
            'left'
        );

        $select->join(array('incomeInvoiceMain' => $this->permissionIncomeInvoiceMainTableName),
            $this->table . '.incomeInvoiceId = incomeInvoiceMain.id',
            $this->permissionIncomeInvoiceMainTableFieldsMap,
            'left'
        );

        $select->join(array('preInvoice' => $this->permissionPreInvoiceMainTableName),
            'incomeInvoiceMain.preInvoiceId = preInvoice.id',
            $this->permissionPreInvoiceTableFieldsMap,
            'left'
        );

        $select->join(array('flight' => $this->flightTableName),
            'preInvoice.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left'
        );

        $select->join(
            array('preInvoiceAgent' => 'library_kontragent'),
            'preInvoice.agentId = preInvoiceAgent.id',
            array(
                'preInvoiceAgentName' => 'name',
                'preInvoiceAgentShortName' => 'short_name',
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
            array('preInvoiceAirport' => 'library_airport'),
            'preInvoice.airportId = preInvoiceAirport.id',
            array(
                'preInvoiceAirportName' => 'name',
                'preInvoiceAirportShortName' => 'short_name',
                'preInvoiceAirportICAO' => 'code_icao',
                'preInvoiceAirportIATA' => 'code_iata',
            ),
            'left');


        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->outcomeInvoiceMainDate = date('d-m-Y', $row->outcomeInvoiceMainDate);
        $row->outcomeInvoiceMainDateArr = date('d-m-Y', $row->outcomeInvoiceMainDateArr);
        $row->outcomeInvoiceMainDateDep = date('d-m-Y', $row->outcomeInvoiceMainDateDep);

        return $row;
    }

    /**
     * @param $customerId
     * @return string
     */
    public function generateNewInvoiceNumber($customerId)
    {
        $customerId = (int)$customerId;
        $select = $this->getSql()->select();
        $select->order(array('id ' . $select::ORDER_DESCENDING));
        $select->limit(1);
        $row = $this->selectWith($select)->current();
        $lastInvoiceId = (int)$row->invoiceId + 1;
        if (!$row) {
            $lastInvoiceId = 1;
        }

        return sprintf('%04d', $customerId) . '-' . sprintf('%08d', $lastInvoiceId);
    }
}
