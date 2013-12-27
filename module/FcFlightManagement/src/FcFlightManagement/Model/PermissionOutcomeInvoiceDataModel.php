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
 * Class PermissionOutcomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class PermissionOutcomeInvoiceDataModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomePermissionData';

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
        $data['dateDep'] = \DateTime::createFromFormat('d-m-Y', $data['dateDep'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateArr'] = \DateTime::createFromFormat('d-m-Y', $data['dateArr'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->permissionOutcomeInvoiceDataTableFieldsMap);

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
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByInvoiceId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->permissionOutcomeInvoiceDataTableFieldsMap);

        $select->join(array('incomeInvoiceData' => $this->permissionIncomeInvoiceDataTableName),
            $this->table . '.incomeInvoiceId = incomeInvoiceData.id',
            $this->permissionIncomeInvoiceDataTableFieldsMap,
            'left'
        );

        $select->join(array('incomeInvoiceMain' => $this->permissionIncomeInvoiceMainTableName),
            'incomeInvoiceData.invoiceId = incomeInvoiceMain.id',
            $this->permissionIncomeInvoiceMainTableFieldsMap,
            'left'
        );

        $select->join(array('preInvoice' => $this->permissionPreInvoiceMainTableName),
            'incomeInvoiceData.preInvoiceId = preInvoice.id',
            $this->permissionPreInvoiceTableFieldsMap,
            'left'
        );

        $select->join(
            array('flight' => $this->flightTableName),
            'preInvoice.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left');

        $select->join(
            array('flightAgent' => 'library_kontragent'),
            'flight.kontragent = flightAgent.id',
            array(
                'flightAgentName' => 'name',
                'flightAgentShortName' => 'short_name',
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
            array('outcomeInvoiceDataAircraft' => 'library_aircraft'),
            $this->table . '.aircraftId = outcomeInvoiceDataAircraft.id',
            array(
                'outcomeInvoiceDataAircraftTypeId' => 'aircraft_type',
                'outcomeInvoiceDataAircraftName' => 'reg_number',
            ),
            'left');

        $select->join(
            array('outcomeInvoiceDataAircraftType' => 'library_aircraft_type'),
            'outcomeInvoiceDataAircraft.aircraft_type = outcomeInvoiceDataAircraftType.id',
            array(
                'outcomeInvoiceDataAircraftTypeName' => 'name',
            ),
            'left');

        $select->join(
            array('leg' => $this->legTableName),
            'preInvoice.legId = leg.id',
            $this->legTableFieldsMap,
            'left');


        $select->join(
            array('legAirportDep' => 'library_airport'),
            'leg.apDepAirportId = legAirportDep.id',
            array(
                'legAirportDepName' => 'name',
                'legAirportDepShortName' => 'short_name',
                'legAirportDepICAO' => 'code_icao',
                'legAirportDepIATA' => 'code_iata',
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

        $select->join(
            array('preInvoiceCountry' => 'library_country'),
            'preInvoice.countryId = preInvoiceCountry.id',
            array(
                'preInvoiceCountryName' => 'name',
            ),
            'left');

        $select->join(
            array('outcomeInvoiceDataUnit' => 'library_unit'),
            $this->table . '.unitId = outcomeInvoiceDataUnit.id',
            array(
                'outcomeInvoiceDataUnitName' => 'name',
            ),
            'left');

        $select->where(array(
            $this->table . '.invoiceId' => $id,
        ));
        $select->order(array($this->table . '.id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
