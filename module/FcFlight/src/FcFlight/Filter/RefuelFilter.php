<?php
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class RefuelFilter implements InputFilterAwareInterface
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
                            'min' => 6,
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
                            'callback' => function($value, $context = array()) use (&$apDepTimeValue) {
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
    public function getApDepTimeValue() {
        return $this->apDepTimeValue;
    }
}
