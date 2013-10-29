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
    protected $table = '';

    /**
     * @var string
     */
    protected $apDepTimeValue = '';

    //Real fields
    public $id;
    public $headerId;
    public $dateOfFlight;
    public $flightNumberAirportId;
    public $flightNumberText;
    public $apDepAirportId;
    public $apDepTime;
    public $apArrAirportId;
    public $apArrTime;

    //Virtual fields
    public $flightNumberIcao;
    public $flightNumberIata;
    public $apDepAirports;
    public $apArrAirports;
    public $apDepIcao;
    public $apDepIata;
    public $apArrIcao;
    public $apArrIata;

    public $apDepCityName;
    public $apDepCountryId;
    public $apDepCountryName;
    public $apDepCountryCode;

    public $apArrCityName;
    public $apArrCountryId;
    public $apArrCountryName;
    public $apArrCountryCode;

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
        //Real fields
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->headerId = (isset($data['headerId'])) ? $data['headerId'] : null;
        $this->dateOfFlight = (isset($data['dateOfFlight'])) ? $data['dateOfFlight'] : null;

        $this->apDepAirportId = (isset($data['apDepAirportId'])) ? $data['apDepAirportId'] : null;
        $this->apArrAirportId = (isset($data['apArrAirportId'])) ? $data['apArrAirportId'] : null;

        if (isset($data['flightNumber']['flightNumberAirportId'])) {
            $this->flightNumberAirportId = $data['flightNumber']['flightNumberAirportId'];
        } elseif (isset($data['flightNumberAirportId'])) {
            $this->flightNumberAirportId = $data['flightNumberAirportId'];
        } else {
            $this->flightNumberAirportId = null;
        }
        if (isset($data['flightNumber']['flightNumberText'])) {
            $this->flightNumberText = $data['flightNumber']['flightNumberText'];
        } elseif (isset($data['flightNumberText'])) {
            $this->flightNumberText = $data['flightNumberText'];
        } else {
            $this->flightNumberText = null;
        }
        if (isset($data['apDep']['apDepAirports'])) {
            $this->apDepAirports = $data['apDep']['apDepAirports'];
        } elseif (isset($data['apDepAirports'])) {
            $this->apDepAirports = $data['apDepAirports'];
        } else {
            $this->apDepAirports = null;
        }
        if (isset($data['apDep']['apDepTime'])) {
            $this->apDepTime = $data['apDep']['apDepTime'];
        } elseif (isset($data['apDepTime'])) {
            $this->apDepTime = $data['apDepTime'];
        } else {
            $this->apDepTime = null;
        }
        if (isset($data['apArr']['apArrAirports'])) {
            $this->apArrAirports = $data['apArr']['apArrAirports'];
        } elseif (isset($data['apArrAirports'])) {
            $this->apArrAirports = $data['apArrAirports'];
        } else {
            $this->apArrAirports = null;
        }
        if (isset($data['apArr']['apArrTime'])) {
            $this->apArrTime = $data['apArr']['apArrTime'];
        } elseif (isset($data['apArrTime'])) {
            $this->apArrTime = $data['apArrTime'];
        } else {
            $this->apArrTime = null;
        }

        //Virtual fields
        $this->flightNumberIcao = (isset($data['flightNumberIcao'])) ? $data['flightNumberIcao'] : null;
        $this->flightNumberIata = (isset($data['flightNumberIata'])) ? $data['flightNumberIata'] : null;
        $this->apDepAirports = (isset($data['apDepAirports'])) ? $data['apDepAirports'] : null;
        $this->apArrAirports = (isset($data['apArrAirports'])) ? $data['apArrAirports'] : null;
        $this->apDepIcao = (isset($data['apDepIcao'])) ? $data['apDepIcao'] : null;
        $this->apDepIata = (isset($data['apDepIata'])) ? $data['apDepIata'] : null;
        $this->apArrIcao = (isset($data['apArrIcao'])) ? $data['apArrIcao'] : null;
        $this->apArrIata = (isset($data['apArrIata'])) ? $data['apArrIata'] : null;

        $this->apDepCityName = (isset($data['apDepCityName'])) ? $data['apDepCityName'] : null;
        $this->apDepCountryId = (isset($data['apDepCountryId'])) ? $data['apDepCountryId'] : null;
        $this->apDepCountryName = (isset($data['apDepCountryName'])) ? $data['apDepCountryName'] : null;
        $this->apDepCountryCode = (isset($data['apDepCountryCode'])) ? $data['apDepCountryCode'] : null;

        $this->apArrCityName = (isset($data['apArrCityName'])) ? $data['apArrCityName'] : null;
        $this->apArrCountryId = (isset($data['apArrCountryId'])) ? $data['apArrCountryId'] : null;
        $this->apArrCountryName = (isset($data['apArrCountryName'])) ? $data['apArrCountryName'] : null;
        $this->apArrCountryCode = (isset($data['apArrCountryCode'])) ? $data['apArrCountryCode'] : null;
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
                'name' => 'dateOfFlight',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'd-m-Y',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be dd-mm-YY',
                                'dateInvalidDate' => 'Invalid date, must be dd-mm-YY'
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

            $flightNumberInputFilter = new InputFilter();

            $flightNumberInputFilter->add($factory->createInput(array(
                'name' => 'flightNumberAirportId',
                'required' => true,
            )));

            $flightNumberInputFilter->add($factory->createInput(array(
                'name' => 'flightNumberText',
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

            $inputFilter->add($flightNumberInputFilter, 'flightNumber');

            $apDepInputFilter = new InputFilter();

            $apDepInputFilter->add($factory->createInput(array(
                'name' => 'apDepAirports',
                'required' => true,
            )));

            $apDepInputFilter->add($factory->createInput(array(
                'name' => 'apDepTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'H:i',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function ($value, $context = array()) use (&$apDepTimeValue) {
                                $apDepTimeValue = $value;
                                return true;
                            },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($apDepInputFilter, 'apDep');

            $apArrInputFilter = new InputFilter();

            $apArrInputFilter->add($factory->createInput(array(
                'name' => 'apArrAirports',
                'required' => true,
            )));

            $apArrInputFilter->add($factory->createInput(array(
                'name' => 'apArrTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'H:i',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'The arrival time is less than the departure time',
                            ),
                            'callback' => function ($value, $context = array()) use (&$apDepTimeValue) {
                                $apArrTime = \DateTime::createFromFormat('H:i', $value);
                                $apDepTime = \DateTime::createFromFormat('H:i', $apDepTimeValue);
                                return $apArrTime > $apDepTime;
                            },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($apArrInputFilter, 'apArr');

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param $value
     * @deprecated
     */
    public function setApDepTimeValue($value)
    {
        $this->apDepTimeValue = $value;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getApDepTimeValue()
    {
        return $this->apDepTimeValue;
    }
}
