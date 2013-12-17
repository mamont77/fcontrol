<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class ApServiceIncomeInvoiceSearchModel
 * @package FcFlightManagement\Model
 */
class ApServiceIncomeInvoiceSearchModel extends BaseModel
{
    /**
     * Name of the variable оставлено для совместимости с AbstractTableGateway
     *
     * @var string
     */
    public $table = 'flightApServiceForm';

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
        $select->columns($this->preInvoiceTableFieldsMap);

        $select->join(
            array('flight' => $this->flightTableName),
            $this->table . '.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left');

        $select->join(
            array('leg' => $this->legTableName),
            $this->table . '.legId = leg.id',
            $this->legTableFieldsMap,
            'left');

        $select->join(
            array('incomeInvoiceMain' => $this->incomeInvoiceMainTableName),
            $this->table . '.id = incomeInvoiceMain.preInvoiceId',
            $this->incomeInvoiceMainTableFieldsMap,
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
            array('preInvoiceAirport' => 'library_airport'),
            $this->table . '.airportId = preInvoiceAirport.id',
            array(
                'preInvoiceAirportName' => 'name',
                'preInvoiceAirportShortName' => 'short_name',
                'preInvoiceAirportICAO' => 'code_icao',
                'preInvoiceAirportIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceAgent' => 'library_kontragent'),
            $this->table . '.agentId = preInvoiceAgent.id',
            array(
                'preInvoiceAgentName' => 'name',
                'preInvoiceAgentShortName' => 'short_name',
            ),
            'left');

        $select->join(
            array('preInvoiceTypeOfService' => 'library_type_of_ap_service'),
            $this->table . '.typeOfApServiceId = preInvoiceTypeOfService.id',
            array(
                'preInvoiceTypeOfServiceName' => 'name',
            ),
            'left');

        if ($data['dateFrom'] != '' && $data['dateTo'] != '') {
            $select->where->between('flight.dateOrder', $data['dateFrom'], $data['dateTo']);
        } else {
            if ($data['dateFrom'] != '') {
                $select->where->greaterThanOrEqualTo('flight.dateOrder', $data['dateFrom']);
            }

            if ($data['dateTo'] != '') {
                $select->where->lessThanOrEqualTo('flight.dateOrder', $data['dateTo']);
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
            $select->where->equalTo($this->table . '.agentId', $data['agentId']);
        }

        if ($data['airportId'] != '') {
            $select->where->equalTo($this->table . '.airportId', $data['airportId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo('flight.kontragent', $data['customerId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo('flight.airOperator', $data['airOperatorId']);
        }

        if (!empty($data['rowsSelected'])) {
            $select->where->in($this->table . '.id', $data['rowsSelected']);
        }

        $select->order($this->table . '.id ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString()); exit;
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
