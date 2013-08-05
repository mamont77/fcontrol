<?php
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;
use FcFlight\Validator\FcFlightDateOfFlight;

class FlightDataFilter implements InputFilterAwareInterface
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
    public $flightNumberIcaoAndIata;
    public $flightNumberText;
    public $apDepIcaoAndIata;
    public $apDepTime;
    public $apArrIcaoAndIata;
    public $apArrTime;

    //Virtual fields
    public $flightNumberIcao;
    public $flightNumberIata;
    public $apDepIcao;
    public $apDepIata;
    public $apArrIcao;
    public $apArrIata;

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
        if (isset($data['flightNumber']['flightNumberIcaoAndIata'])) {
            $this->flightNumberIcaoAndIata = $data['flightNumber']['flightNumberIcaoAndIata'];
        } else if (isset($data['flightNumberIcaoAndIata'])) {
            $this->flightNumberIcaoAndIata = $data['flightNumberIcaoAndIata'];
        } else {
            $this->flightNumberIcaoAndIata = null;
        }
        if (isset($data['flightNumber']['flightNumberText'])) {
            $this->flightNumberText = $data['flightNumber']['flightNumberText'];
        } else if (isset($data['flightNumberText'])) {
            $this->flightNumberText = $data['flightNumberText'];
        } else {
            $this->flightNumberText = null;
        }
        if (isset($data['apDep']['apDepIcaoAndIata'])) {
            $this->apDepIcaoAndIata = $data['apDep']['apDepIcaoAndIata'];
        } else if (isset($data['apDepIcaoAndIata'])) {
            $this->apDepIcaoAndIata = $data['apDepIcaoAndIata'];
        } else {
            $this->apDepIcaoAndIata = null;
        }
        if (isset($data['apDep']['apDepTime'])) {
            $this->apDepTime = $data['apDep']['apDepTime'];
        } else if (isset($data['apDepTime'])) {
            $this->apDepTime = $data['apDepTime'];
        } else {
            $this->apDepTime = null;
        }
        if (isset($data['apArr']['apArrIcaoAndIata'])) {
            $this->apArrIcaoAndIata = $data['apArr']['apArrIcaoAndIata'];
        } else if (isset($data['apArrIcaoAndIata'])) {
            $this->apArrIcaoAndIata = $data['apArrIcaoAndIata'];
        } else {
            $this->apArrIcaoAndIata = null;
        }
        if (isset($data['apArr']['apArrTime'])) {
            $this->apArrTime = $data['apArr']['apArrTime'];
        } else if (isset($data['apArrTime'])) {
            $this->apArrTime = $data['apArrTime'];
        } else {
            $this->apArrTime = null;
        }

        //Virtual fields
        $this->flightNumberIcao = (isset($data['flightNumberIcao'])) ? $data['flightNumberIcao'] : null;
        $this->flightNumberIata = (isset($data['flightNumberIata'])) ? $data['flightNumberIata'] : null;
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
                'required' => true,
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
                        'name' => 'FcFlight\Validator\dateOfFlight',
                    )
                ),
            )));

            $flightNumberInputFilter = new InputFilter();

            $flightNumberInputFilter->add($factory->createInput(array(
                'name' => 'flightNumberIcaoAndIata',
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
                'name' => 'apDepIcaoAndIata',
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
                'name' => 'apArrIcaoAndIata',
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
