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
     * @param $data
     */
    public function exchangeArray($data)
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
        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();
//            \Zend\Debug\Debug::dump($inputFilter);

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

//            $inputFilter->add($factory->createInput(array(
//                'name' => 'flightNumberId',
//                'required' => true,
//            )));
//
//            $inputFilter->add($factory->createInput(array(
//                'name' => 'flightNumberText',
//                'required' => true,
//                'filters' => $this->defaultFilters,
//                'validators' => array(
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min' => 6,
//                            'max' => 6,
//                        ),
//                    ),
//                ),
//            )));
//
//            $inputFilter->add($factory->createInput(array(
//                'name' => 'apDepId',
//                'required' => true,
//            )));
//
//            $inputFilter->add($factory->createInput(array(
//                'name' => 'apDepTime',
//                'required' => true,
//                'filters' => $this->defaultFilters,
//                'validators' => array(
//                    array(
//                        'name' => 'Date',
//                        'format' => 'h:m',
//                    ),
//                ),
//            )));
//
//            $inputFilter->add($factory->createInput(array(
//                'name' => 'apArrId',
//                'required' => true,
//            )));
//
//            $inputFilter->add($factory->createInput(array(
//                'name' => 'apArrTime',
//                'required' => true,
//                'filters' => $this->defaultFilters,
//                'validators' => array(
//                    array(
//                        'name' => 'Date',
//                        'format' => 'h:m',
//                    ),
//                ),
//            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
