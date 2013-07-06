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
    public $flightNumberId;
    public $flightNumberText;
    public $apDepId;
    public $apDepTime;
    public $apArrId;
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
        \Zend\Debug\Debug::dump($data);
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->parentFormId = (isset($data['parentFormId'])) ? $data['parentFormId'] : null;
        $this->dateOfFlight = (isset($data['dateOfFlight'])) ? $data['dateOfFlight'] : null;
        $this->flightNumberId = (isset($data['flightNumberId'])) ? $data['flightNumberId'] : null;
        $this->flightNumberText = (isset($data['flightNumberText'])) ? $data['flightNumberText'] : null;
        $this->apDepId = (isset($data['apDepId'])) ? $data['apDepId'] : null;
        $this->apDepTime = (isset($data['apDepTime'])) ? $data['apDepTime'] : null;
        $this->apArrId = (isset($data['apArrId'])) ? $data['apArrId'] : null;
        $this->apArrTime = (isset($data['apArrTime'])) ? $data['apArrTime'] : null;
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

            $inputFilter->add($factory->createInput(array(
                'name' => 'dateOfFlight',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'format' => 'd-m-Y',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'flightNumberId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
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

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apDepTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'format' => 'h:m',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'apArrTime',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'format' => 'h:m',
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
