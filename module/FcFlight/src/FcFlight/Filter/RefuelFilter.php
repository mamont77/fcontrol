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

    //Real fields
    public $id;
    public $headerId;
    public $airport;
    public $date;
    public $agent;
    public $quantity;
    public $unit;

    //Virtual fields
    public $airportName;
    public $airportIcao;
    public $airportIata;
    public $agentName;
    public $unitName;

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
        $this->airport = (isset($data['airport'])) ? $data['airport'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;
        $this->agent = (isset($data['agent'])) ? $data['agent'] : null;
        $this->quantity = (isset($data['quantity'])) ? $data['quantity'] : null;
        $this->unit = (isset($data['unit'])) ? $data['unit'] : null;
        $this->airportName = (isset($data['airportName'])) ? $data['airportName'] : null;
        $this->airportIcao = (isset($data['airportIcao'])) ? $data['airportIcao'] : null;
        $this->airportIata = (isset($data['airportIata'])) ? $data['airportIata'] : null;
        $this->agentName = (isset($data['agentName'])) ? $data['agentName'] : null;
        $this->unitName = (isset($data['unitName'])) ? $data['unitName'] : null;
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
                'name' => 'headerId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airport',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'date',
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

            $inputFilter->add($factory->createInput(array(
                'name' => 'agent',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'quantity',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^(([^0]{1})([0-9])*|(0{1}))(\.\d{2})?$/',
                            'messages' => array(
                                'regexNotMatch' => 'Invalid quantity format. For example please enter: 100 or 500.50',
                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'unit',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
