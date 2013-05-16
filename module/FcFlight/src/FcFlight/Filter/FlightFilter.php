<?php
namespace FcFlight\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class FlightFilter implements InputFilterAwareInterface
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
    public $dateOrder;
    public $kontragent;
    public $airOperator;
    public $aircraft;

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

//        \Zend\Debug\Debug::dump($data);
//        exit;
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->dateOrder = (isset($data['dateOrder'])) ? $data['dateOrder'] : null;
        $this->kontragent = (isset($data['kontragent'])) ? $data['kontragent'] : null;
        $this->airOperator = (isset($data['airOperator'])) ? $data['airOperator'] : null;
        $this->aircraft = (isset($data['aircraft'])) ? $data['aircraft'] : null;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @return void|\Zend\InputFilter\InputFilterAwareInterface
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
//                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'encoding' => 'UTF-8',
//                            'format' => 'yymmdd',
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
