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
 * Class RefuelFilter
 * @package FcFlight\Filter
 */
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
    public $table = '';

    //Fields for form and view
    public $id;
    public $headerId;
    public $agentId;
    public $legId;
    public $airportId;
    public $quantityLtr;
    public $quantityOtherUnits;
    public $unitId;
    public $priceUsd;
    public $totalPriceUsd;
    public $date;


    //Fields only for view
    public $icao;
    public $iata;
    public $airportName;
    public $typeOfApServiceName;
    public $kontragentShortName;
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
        //Fields for form and view
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->headerId = (isset($data['headerId'])) ? $data['headerId'] : null;
        $this->agentId = (isset($data['agentId'])) ? $data['agentId'] : null;
        $this->legId = (isset($data['legId'])) ? $data['legId'] : null;
        $this->airportId = (isset($data['airportId'])) ? $data['airportId'] : null;
        $this->quantityLtr = (isset($data['quantityLtr'])) ? $data['quantityLtr'] : null;
        $this->quantityOtherUnits = (isset($data['quantityOtherUnits'])) ? $data['quantityOtherUnits'] : null;
        $this->unitId = (isset($data['unitId'])) ? $data['unitId'] : null;
        $this->priceUsd = (isset($data['priceUsd'])) ? $data['priceUsd'] : null;
        $this->totalPriceUsd = (isset($data['totalPriceUsd'])) ? $data['totalPriceUsd'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;

        //Fields only for view
        $this->icao = (isset($data['icao'])) ? $data['icao'] : null;
        $this->iata = (isset($data['iata'])) ? $data['iata'] : null;
        $this->airportName = (isset($data['airportName'])) ? $data['airportName'] : null;
        $this->typeOfApServiceName = (isset($data['typeOfApServiceName'])) ? $data['typeOfApServiceName'] : null;
        $this->kontragentShortName = (isset($data['kontragentShortName'])) ? $data['kontragentShortName'] : null;
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

//            $inputFilter->add($factory->createInput(array(
//                'name' => 'previousDate',
//                'required' => false,
//            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'agentId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airportId',
                'required' => true,
                'filters' => $this->defaultFilters,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'quantityLtr',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
//                    array(
//                        'name' => 'Regex',
//                        'options' => array(
//                            'pattern' => '/^(([^0]{1})([0-9])*|(0{1}))(\.\d{2})?$/',
//                            'messages' => array(
//                                'regexNotMatch' => 'Invalid quantity format. For example please enter: 100 or 500.50',
//                            ),
//                        ),
//                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'quantityOtherUnits',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
//                    array(
//                        'name' => 'Regex',
//                        'options' => array(
//                            'pattern' => '/^(([^0]{1})([0-9])*|(0{1}))(\.\d{2})?$/',
//                            'messages' => array(
//                                'regexNotMatch' => 'Invalid quantity format. For example please enter: 100 or 500.50',
//                            ),
//                        ),
//                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'unitId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'priceUsd',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
//                    array(
//                        'name' => 'Regex',
//                        'options' => array(
//                            'pattern' => '/^(([^0]{1})([0-9])*|(0{1}))(\.\d{2})?$/',
//                            'messages' => array(
//                                'regexNotMatch' => 'Invalid quantity format. For example please enter: 100 or 500.50',
//                            ),
//                        ),
//                    ),
                ),
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
//                    array(
//                        'name' => 'FcFlight\Validator\FlightDateChecker',
//                    ),
//                    array(
//                        'name' => 'FcFlight\Validator\FlightYearChecker',
//                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
