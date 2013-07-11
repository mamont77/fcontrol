<?php
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

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

    public $id;
    public $parentFormId;
    public $dateOfFlight;
    public $flightNumberIdIcao;
    public $flightNumberIdIata;
    public $flightNumberText;
    public $apDepIdIcao;
    public $apDepIdIata;
    public $apDepTime;
    public $apArrIdIcao;
    public $apArrIdIata;
    public $apArrTime;

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
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->parentFormId = (isset($data['parentFormId'])) ? $data['parentFormId'] : null;
        $this->dateOfFlight = (isset($data['dateOfFlight'])) ? $data['dateOfFlight'] : null;
        $this->flightNumberIdIcao = (isset($data['flightNumber']['flightNumberIdIcao']))
            ? $data['flightNumber']['flightNumberIdIcao'] : null;
        $this->flightNumberIdIata = (isset($data['flightNumber']['flightNumberIdIata']))
            ? $data['flightNumber']['flightNumberIdIata'] : null;
        $this->flightNumberText = (isset($data['flightNumber']['flightNumberText']))
            ? $data['flightNumber']['flightNumberText'] : null;
        $this->apDepIdIcao = (isset($data['apDep']['apDepIdIcao'])) ? $data['apDep']['apDepIdIcao'] : null;
        $this->apDepIdIata = (isset($data['apDep']['apDepIdIata'])) ? $data['apDep']['apDepIdIata'] : null;
        $this->apDepTime = (isset($data['apDep']['apDepTime'])) ? $data['apDep']['apDepTime'] : null;
        $this->apArrIdIcao = (isset($data['apArr']['apArrIdIcao'])) ? $data['apArr']['apArrIdIcao'] : null;
        $this->apArrIdIata = (isset($data['apArr']['apArrIdIata'])) ? $data['apArr']['apArrIdIata'] : null;
        $this->apArrTime = (isset($data['apArr']['apArrTime'])) ? $data['apArr']['apArrTime'] : null;
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
                'name' => 'parentFormId',
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
                ),
            )));

            $flightNumberInputFilter = new InputFilter();

            $flightNumberInputFilter->add($factory->createInput(array(
                'name' => 'flightNumberIdIcao',
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
                            'min' => 6,
                            'max' => 6,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($flightNumberInputFilter, 'flightNumber');

            $apDepInputFilter = new InputFilter();

            $apDepInputFilter->add($factory->createInput(array(
                'name' => 'apDepIdIcao',
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
                            'callback' => function($value, $context = array()) use (&$apDepTimeValue) {
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
                'name' => 'apArrIdIcao',
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
                            'callback' => function($value, $context = array()) use (&$apDepTimeValue) {
                                // value of this input
                                $apArrTime = \DateTime::createFromFormat('H:i', $value);
                                // value of input to check against from context
                                $apDepTime = \DateTime::createFromFormat('H:i', $apDepTimeValue);
                                // compare times, eg..
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
    public function getApDepTimeValue() {
        return $this->apDepTimeValue;
    }
}
