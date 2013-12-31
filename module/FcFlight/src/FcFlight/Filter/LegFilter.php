<?php
/**
 * @namespace
 */
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class LegFilter
 * @package FcFlight\Filter
 */
class LegFilter implements InputFilterAwareInterface
{

    /**
     * @var $inputFilter
     */
    protected $inputFilter;

    /**
     * @var $dbAdapter
     */
    protected $dbAdapter;

    /**
     * @var string
     */
    public $table = '';

    //Fields for form and view
    public $id;
    public $headerId;
    public $airOperatorId;
    public $flightNumber;
    public $apDepAirportId;
    public $apDepTime;
    public $apArrAirportId;
    public $apArrTime;

    //Fields only for view
    public $airOperatorIcao;
    public $airOperatorIata;
    public $apDepCountries;
    public $apArrCountries;
    public $apDepAirports;
    public $apArrAirports;
    public $apDepIcao;
    public $apDepIata;
    public $apArrIcao;
    public $apArrIata;
    public $apDepCountryId;
    public $apArrCountryId;

    /**
     * @var array
     */
    protected $defaultFilters = array(
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
    );

    /**
     * @param \Zend\Db\Adapter\Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * Array to Object
     *
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        //Fields for form and view
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->headerId = (isset($data['headerId'])) ? $data['headerId'] : null;
        $this->apDepCountryId = (isset($data['apDepCountryId'])) ? $data['apDepCountryId'] : null;
        $this->apArrCountryId = (isset($data['apArrCountryId'])) ? $data['apArrCountryId'] : null;
        $this->apDepAirportId = (isset($data['apDepAirportId'])) ? $data['apDepAirportId'] : null;
        $this->apArrAirportId = (isset($data['apArrAirportId'])) ? $data['apArrAirportId'] : null;

        $this->airOperatorId = (isset($data['airOperatorId'])) ? $data['airOperatorId'] : null;
        $this->flightNumber = (isset($data['flightNumber'])) ? $data['flightNumber'] : null;
        if (isset($data['apDepAirports'])) {
            $this->apDepAirports = $data['apDepAirports'];
        } elseif (isset($data['apDepAirportId'])) {
            $this->apDepAirports = $data['apDepAirportId'];
        } else {
            $this->apDepAirports = null;
        }
        $this->apDepTime = (isset($data['apDepTime'])) ? $data['apDepTime'] : null;
        $this->apArrCountryId = (isset($data['apArrCountryId'])) ? $data['apArrCountryId'] : null;
        if (isset($data['apArrAirports'])) {
            $this->apArrAirports = $data['apArrAirports'];
        } elseif (isset($data['apArrAirportId'])) {
            $this->apArrAirports = $data['apArrAirportId'];
        } else {
            $this->apArrAirports = null;
        }
        $this->apArrTime = (isset($data['apArrTime'])) ? $data['apArrTime'] : null;

        //Fields only for view
        $this->airOperatorIcao = (isset($data['airOperatorIcao'])) ? $data['airOperatorIcao'] : null;
        $this->airOperatorIata = (isset($data['airOperatorIata'])) ? $data['airOperatorIata'] : null;

        $this->apDepIcao = (isset($data['apDepIcao'])) ? $data['apDepIcao'] : null;
        $this->apDepIata = (isset($data['apDepIata'])) ? $data['apDepIata'] : null;

        $this->apArrIcao = (isset($data['apArrIcao'])) ? $data['apArrIcao'] : null;
        $this->apArrIata = (isset($data['apArrIata'])) ? $data['apArrIata'] : null;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }


    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        global $apDepTimeValue;

        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'headerId',
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'previousDate',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'preSelectedAirOperatorId',
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'preSelectedApDepCountryId',
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'preSelectedApDepAirportId',
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepAirportId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrAirportId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airOperatorId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'flightNumber',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 6,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'd-m-Y H:i',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be dd-mm-YYYY HH:ii',
                                'dateInvalidDate' => 'Invalid date, must be dd-mm-YYYY HH:ii',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'FcFlight\Validator\FlightDateChecker',
                    ),
                    array(
                        'name' => 'FcFlight\Validator\FlightYearChecker',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepCountryId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepAirports',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'd-m-Y H:i',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE =>
                                    'The arrival time is less than the departure time',
                            ),
                            'callback' => function ($value, $context) {
                                    $apArrTime = \DateTime::createFromFormat('d-m-Y H:i', $value);
                                    $apDepTime = \DateTime::createFromFormat('d-m-Y H:i', $context['apDepTime']);
                                    return $apArrTime > $apDepTime;
                                },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrCountryId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrAirports',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
