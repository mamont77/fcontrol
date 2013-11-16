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
 * Class ApServiceFilter
 * @package FcFlight\Filter
 */
class ApServiceFilter implements InputFilterAwareInterface
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
    public $legId;
    public $airportId;
    public $typeOfApServiceId;
    public $agentId;
    public $price;
    public $currency;
    public $exchangeRate;
    public $priceUSD;

    //Virtual fields
    public $icao;
    public $iata;
    public $airportName;
    public $typeOfApServiceName;
    public $kontragentShortName;

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
        $this->legId = (isset($data['legId'])) ? $data['legId'] : null;
        $this->airportId = (isset($data['airportId'])) ? $data['airportId'] : null;
        $this->typeOfApServiceId = (isset($data['typeOfApServiceId'])) ? $data['typeOfApServiceId'] : null;
        $this->agentId = (isset($data['agentId'])) ? $data['agentId'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        $this->currency = (isset($data['currency'])) ? $data['currency'] : null;
        $this->exchangeRate = (isset($data['exchangeRate'])) ? $data['exchangeRate'] : null;
        $this->priceUSD = (isset($data['priceUSD'])) ? $data['priceUSD'] : null;

        //Virtual fields
        $this->icao = (isset($data['icao'])) ? $data['icao'] : null;
        $this->iata = (isset($data['iata'])) ? $data['iata'] : null;
        $this->airportName = (isset($data['airportName'])) ? $data['airportName'] : null;
        $this->typeOfApServiceName = (isset($data['$typeOfApServiceName'])) ? $data['$typeOfApServiceName'] : null;
        $this->kontragentShortName = (isset($data['kontragentShortName'])) ? $data['kontragentShortName'] : null;
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
                'name' => 'typeOfApServiceId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'agentId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'price',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 32,
                        ),
                    ),
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'currency',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'exchangeRate',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 16,
                        ),
                    ),
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'priceUSD',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 32,
                        ),
                    ),
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'locale' => 'en',
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
