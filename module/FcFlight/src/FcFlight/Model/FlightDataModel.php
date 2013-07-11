<?php

namespace FcFlight\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use FcFlight\Filter\FlightDataFilter;

class FlightDataModel extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'flightBaseDataForm';

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new FlightDataFilter($this->adapter));
        $this->initialize();
    }

    /**
     * @param FlightDataFilter $object
     * @return string
     */
    public function add(FlightDataFilter $object)
    {
        $dateOfFlight = \DateTime::createFromFormat('d-m-Y', $object->dateOfFlight);
        $apDepTime = \DateTime::createFromFormat('d-m-Y H:i', $object->dateOfFlight . ' ' . $object->apDepTime);
        $apArrTime = \DateTime::createFromFormat('d-m-Y H:i', $object->dateOfFlight . ' ' . $object->apArrTime);

        $data = array(
            'parentFormId' => $object->parentFormId,
            'dateOfFlight' => $dateOfFlight->getTimestamp(),
            'flightNumberIcaoAndIata' => $object->flightNumberIcaoAndIata,
            'flightNumberText' => $object->flightNumberText,
            'apDepIcaoAndIata' => $object->apDepIcaoAndIata,
            'apDepTime' => $apDepTime->getTimestamp(),
            'apArrIcaoAndIata' => $object->apArrIcaoAndIata,
            'apArrTime' => $apArrTime->getTimestamp(),
        );
        $hash = $object->dateOfFlight . ': Dep ' . $object->apDepTime . ', Arr ' . $object->apArrTime . '.';

        $this->insert($data);

        return $hash;
    }
}
