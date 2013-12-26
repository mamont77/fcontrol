<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class PermissionOutcomeInvoiceSearchModel
 * @package FcFlightManagement\Model
 */
class PermissionOutcomeInvoiceSearchModel extends BaseModel
{
    /**
     * Name of the variable оставлено для совместимости с AbstractTableGateway
     *
     * @var string
     */
    public $table = 'invoiceIncomePermissionData';

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
        $select->columns($this->permissionIncomeInvoiceDataTableFieldsMap);

        $select->join(
            array('incomeInvoiceMain' => $this->permissionIncomeInvoiceMainTableName),
            $this->table . '.invoiceId = incomeInvoiceMain.id',
            $this->permissionIncomeInvoiceMainTableFieldsMap,
            'left');

        $select->join(
            array('outcomeInvoiceData' => $this->permissionOutcomeInvoiceDataTableName),
            $this->table . '.id = outcomeInvoiceData.incomeInvoiceId',
            $this->permissionOutcomeInvoiceDataTableFieldsMap,
            'left');

        $select->join(
            array('outcomeInvoiceMain' => $this->permissionOutcomeInvoiceMainTableName),
            'outcomeInvoiceData.invoiceId = outcomeInvoiceMain.id',
            $this->permissionOutcomeInvoiceMainTableFieldsMap,
            'left');

        $select->join(
            array('preInvoice' => $this->permissionPreInvoiceMainTableName),
            $this->table . '.preInvoiceId = preInvoice.id',
            $this->permissionPreInvoiceTableFieldsMap,
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
            array('incomeInvoiceDataAircraft' => 'library_aircraft'),
            $this->table . '.aircraftId = incomeInvoiceDataAircraft.id',
            array(
                'incomeInvoiceDataAircraftTypeId' => 'aircraft_type',
                'incomeInvoiceDataAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataAircraftType' => 'library_aircraft_type'),
            'incomeInvoiceDataAircraft.aircraft_type = incomeInvoiceDataAircraftType.id',
            array(
                'incomeInvoiceDataAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('incomeInvoiceDataUnit' => 'library_unit'),
            $this->table . '.unitId = incomeInvoiceDataUnit.id',
            array(
                'incomeInvoiceDataUnitName' => 'name',
            ),
            'left');

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

        if ($data['dateFrom'] != '' && $data['dateTo'] != '') {
            $select->where
                ->NEST
                ->between($this->table . '.dateDep', $data['dateFrom'], $data['dateTo'])
                ->OR
                ->between($this->table . '.dateArr', $data['dateFrom'], $data['dateTo'])
                ->UNNEST;
        } else {
            if ($data['dateFrom'] != '') {
                $select->where
                    ->NEST
                    ->greaterThanOrEqualTo($this->table . '.dateDep', $data['dateFrom'])
                    ->OR
                    ->greaterThanOrEqualTo($this->table . '.dateArr', $data['dateFrom'])
                    ->UNNEST;
            }
            if ($data['dateTo'] != '') {
                $select->where
                    ->NEST
                    ->lessThanOrEqualTo($this->table . '.dateDep', $data['dateFrom'])
                    ->OR
                    ->lessThanOrEqualTo($this->table . '.dateArr', $data['dateFrom'])
                    ->UNNEST;
            }
        }

        if ($data['aircraftId'] != '') {
            $select->where->equalTo($this->table . '.aircraftId', $data['aircraftId']);
        }

        if ($data['agentId'] != '') {
            $select->where->equalTo('incomeInvoiceMain.agentId', $data['agentId']);
        }

        if ($data['countryId'] != '') {
            $select->where->equalTo('preInvoice.countryId', $data['countryId']);
        }

        if ($data['airportDepId'] != '') {
            $select->where->equalTo('leg.airportDepId', $data['airportDepId']);
        }

        if ($data['airportArrId'] != '') {
            $select->where->equalTo('leg.airportArrId', $data['airportArrId']);
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

        $select->where->isNotNull('flight.id');

        if ($data['typeOfInvoice'] == 'both') {
            $select->where->isNotNull('outcomeInvoiceMain.id');
        }

        $select->order($this->table . '.id ' . Select::ORDER_DESCENDING);
//        \Zend\Debug\Debug::dump($select->getSqlString()); exit;
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
