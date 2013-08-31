<?php
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class FlightHeaderFilter implements InputFilterAwareInterface
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
    public $refNumberOrder;
    public $dateOrder;
    public $kontragent;
    public $kontragentShortName;
    public $airOperator;
    public $airOperatorShortName;
    public $aircraft;
    public $aircraftType;
    public $aircraftTypeName;
    public $status;

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
        $this->refNumberOrder = (isset($data['refNumberOrder'])) ? $data['refNumberOrder'] : null;
        $this->dateOrder = (isset($data['dateOrder'])) ? $data['dateOrder'] : null;
        $this->kontragent = (isset($data['kontragent'])) ? $data['kontragent'] : null;
        $this->kontragentShortName = (isset($data['kontragentShortName'])) ? $data['kontragentShortName'] : null;
        $this->airOperator = (isset($data['airOperator'])) ? $data['airOperator'] : null;
        $this->airOperatorShortName = (isset($data['airOperatorShortName'])) ? $data['airOperatorShortName'] : null;
        $this->aircraft = (isset($data['aircraft'])) ? $data['aircraft'] : null;
        $this->aircraftType = (isset($data['aircraftType'])) ? $data['aircraftType'] : null;
        $this->aircraftTypeName = (isset($data['aircraftTypeName'])) ? $data['aircraftTypeName'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
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
                'name' => 'dateOrder',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be YY-mm-dd',
                                'dateInvalidDate' => 'Invalid date, must be YY-mm-dd'
                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'kontragent',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airOperator',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'aircraft',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
