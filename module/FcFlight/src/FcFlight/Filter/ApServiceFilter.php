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
    public $airportId;
    public $isNeed;
    public $agentId;

    //Virtual fields
    public $icao;
    public $iata;
    public $airportName;
    public $kontragentShortName;

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
        $this->airportId = (isset($data['airportId'])) ? $data['airportId'] : null;
        $this->isNeed = (isset($data['isNeed'])) ? $data['isNeed'] : null;
        $this->agentId = (isset($data['agentId'])) ? $data['agentId'] : null;

        //Virtual fields
        $this->icao = (isset($data['icao'])) ? $data['icao'] : null;
        $this->iata = (isset($data['iata'])) ? $data['iata'] : null;
        $this->airportName = (isset($data['airportName'])) ? $data['airportName'] : null;
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
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airportId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'isNeed',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'agentId',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
