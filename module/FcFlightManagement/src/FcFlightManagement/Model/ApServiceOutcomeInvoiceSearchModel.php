<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class ApServiceOutcomeInvoiceSearchModel
 * @package FcFlightManagement\Model
 */
class ApServiceOutcomeInvoiceSearchModel extends BaseModel
{
    /**
     * Name of the variable оставлено для совместимости с AbstractTableGateway
     *
     * @var string
     */
    public $table = 'invoiceIncomeApServiceMain';

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
            array('outcomeInvoice' => $this->apServiceOutcomeInvoiceMainTableName),
            $this->table . '.id = outcomeInvoice.incomeInvoiceId',
            $this->apServiceOutcomeInvoiceMainTableFieldsMap,
            'left');

        $select->join(
            array('flightCustomer' => 'library_kontragent'),
            'flight.kontragent = flightCustomer.id',
            array(
                'flightCustomerName' => 'name',
                'flightCustomerShortName' => 'short_name',
                'flightCustomerAgreement' => 'agreement',
                'flightCustomerTermOfPayment' => 'termOfPayment',
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
            'preInvoice.airportId = preInvoiceAirport.id',
            array(
                'preInvoiceAirportName' => 'name',
                'preInvoiceAirportShortName' => 'short_name',
                'preInvoiceAirportICAO' => 'code_icao',
                'preInvoiceAirportIATA' => 'code_iata',
            ),
            'left');

        $select->join(
            array('preInvoiceAgent' => 'library_kontragent'),
            'preInvoice.agentId = preInvoiceAgent.id',
            array(
                'preInvoiceAgentName' => 'name',
                'preInvoiceAgentShortName' => 'short_name',
                'preInvoiceAgentAgreement' => 'agreement',
                'preInvoiceAgentTermOfPayment' => 'termOfPayment',
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
            array('outcomeInvoiceMainTypeOfService' => 'library_type_of_ap_service'),
            'outcomeInvoice.typeOfServiceId = outcomeInvoiceMainTypeOfService.id',
            array(
                'outcomeInvoiceMainTypeOfServiceName' => 'name',
            ),
            'left');

        $select->join(
            array('outcomeInvoiceMainAircraft' => 'library_aircraft'),
            'outcomeInvoice.aircraftId = outcomeInvoiceMainAircraft.id',
            array(
                'outcomeInvoiceMainAircraftTypeId' => 'aircraft_type',
                'outcomeInvoiceMainAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('outcomeInvoiceMainAircraftType' => 'library_aircraft_type'),
            'outcomeInvoiceMainAircraft.aircraft_type = outcomeInvoiceMainAircraftType.id',
            array(
                'outcomeInvoiceMainAircraftTypeName' => 'name',
            ),
            'left');

        $select->where->equalTo('flight.isYoungest', 1);
        $select->where->in('flight.status', array(0, 1));

        if ($data['dateFrom'] != '' && $data['dateTo'] != '') {
            $select->where->between($this->table . '.dateArr', $data['dateFrom'], $data['dateTo']);
        } else {
            if ($data['dateFrom'] != '') {
                $select->where->greaterThanOrEqualTo($this->table . '.dateArr', $data['dateFrom']);
            }

            if ($data['dateTo'] != '') {
                $select->where->lessThanOrEqualTo($this->table . '.dateArr', $data['dateTo']);
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
            $select->where->equalTo('preInvoice.agentId', $data['agentId']);
        }

        if ($data['airportId'] != '') {
            $select->where->equalTo('preInvoice.airportId', $data['airportId']);
        }

        if ($data['customerId'] != '') {
            $select->where->equalTo('flight.kontragent', $data['customerId']);
        }

        if ($data['airOperatorId'] != '') {
            $select->where->equalTo('flight.airOperator', $data['airOperatorId']);
        }

        if ($data['typeOfInvoice'] == 'both') {
            $select->where->isNotNull('outcomeInvoice.id');
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
