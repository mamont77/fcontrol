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
 * Class FlightHeaderFilter
 * @package FcFlight\Filter
 */
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
    public $table = '';

    public $id;
    public $parentId;
    public $parentRefNumberOrder;
    public $refNumberOrder;
    public $dateOrder;
    public $kontragent;
    public $kontragentShortName;
    public $airOperator;
    public $airOperatorShortName;
    public $aircraftId;
    public $aircraftName;
    public $aircraftTypeName;
    public $alternativeAircraftId1;
    public $alternativeAircraftName1;
    public $alternativeAircraftTypeName1;
    public $alternativeAircraftId2;
    public $alternativeAircraftName2;
    public $alternativeAircraftTypeName2;
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
        $this->parentId = (isset($data['parentId'])) ? $data['parentId'] : null;
        $this->parentRefNumberOrder = (isset($data['parentRefNumberOrder'])) ? $data['parentRefNumberOrder'] : null;
        $this->refNumberOrder = (isset($data['refNumberOrder'])) ? $data['refNumberOrder'] : null;
        $this->dateOrder = (isset($data['dateOrder'])) ? $data['dateOrder'] : null;
        $this->kontragent = (isset($data['kontragent'])) ? $data['kontragent'] : null;
        $this->kontragentShortName = (isset($data['kontragentShortName'])) ? $data['kontragentShortName'] : null;
        $this->airOperator = (isset($data['airOperator'])) ? $data['airOperator'] : null;
        $this->airOperatorShortName = (isset($data['airOperatorShortName'])) ? $data['airOperatorShortName'] : null;
        $this->aircraftId = (isset($data['aircraftId'])) ? $data['aircraftId'] : null;
        $this->aircraftName = (isset($data['aircraftName'])) ? $data['aircraftName'] : null;
        $this->aircraftTypeName = (isset($data['aircraftTypeName'])) ? $data['aircraftTypeName'] : null;
        $this->alternativeAircraftId1 = (isset($data['alternativeAircraftId1'])) ? $data['alternativeAircraftId1'] : null;
        $this->alternativeAircraftName1 = (isset($data['alternativeAircraftName1'])) ? $data['alternativeAircraftName1'] : null;
        $this->alternativeAircraftTypeName1 = (isset($data['alternativeAircraftTypeName1'])) ? $data['alternativeAircraftTypeName1'] : null;
        $this->alternativeAircraftId2 = (isset($data['alternativeAircraftId2'])) ? $data['alternativeAircraftId2'] : null;
        $this->alternativeAircraftName2 = (isset($data['alternativeAircraftName2'])) ? $data['alternativeAircraftName2'] : null;
        $this->alternativeAircraftTypeName2 = (isset($data['alternativeAircraftTypeName2'])) ? $data['alternativeAircraftTypeName2'] : null;
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
                            'format' => 'd-m-Y',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be dd-mm-YY',
                                'dateInvalidDate' => 'Invalid date, must be dd-mm-YY'
                            ),
                        ),
                    ),
                    array(
                        'name' => 'FcFlight\Validator\FlightYearChecker',
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
                'name' => 'aircraftId',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'alternativeAircraftId1',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'alternativeAircraftId2',
                'required' => false,
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
