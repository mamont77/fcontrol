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
 * Class PermissionFilter
 * @package FcFlight\Filter
 */
class PermissionFilter implements InputFilterAwareInterface
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
    public $countryId;
    public $typeOfPermission;
    public $requestTime;
    public $permission;
    public $comment;
    public $status;

    //Fields only for view
    public $agentName;
    public $airportDepartureId;
    public $airportDepartureICAO;
    public $airportDepartureIATA;
    public $airportArrivalId;
    public $airportArrivalICAO;
    public $airportArrivalIATA;
    public $countryName;

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
        $this->countryId = (isset($data['countryId'])) ? $data['countryId'] : null;
        $this->typeOfPermission = (isset($data['typeOfPermission'])) ? $data['typeOfPermission'] : null;
        $this->requestTime = (isset($data['requestTime'])) ? $data['requestTime'] : null;
        $this->permission = (isset($data['permission'])) ? $data['permission'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;

        //Fields only for view
        $this->agentName = (isset($data['agentName'])) ? $data['agentName'] : null;
        $this->agentAddress = (isset($data['agentAddress'])) ? $data['agentAddress'] : null;
        $this->agentMail = (isset($data['agentMail'])) ? $data['agentMail'] : null;
        $this->airportDepartureId = (isset($data['airportDepartureId'])) ? $data['airportDepartureId'] : null;
        $this->airportDepartureICAO = (isset($data['airportDepartureICAO'])) ? $data['airportDepartureICAO'] : null;
        $this->airportDepartureIATA = (isset($data['airportDepartureIATA'])) ? $data['airportDepartureIATA'] : null;
        $this->airportArrivalId = (isset($data['airportArrivalId'])) ? $data['airportArrivalId'] : null;
        $this->airportArrivalICAO = (isset($data['airportArrivalICAO'])) ? $data['airportArrivalICAO'] : null;
        $this->airportArrivalIATA = (isset($data['airportArrivalIATA'])) ? $data['airportArrivalIATA'] : null;
        $this->countryName = (isset($data['countryName'])) ? $data['countryName'] : null;
        $this->countryCode = (isset($data['countryCode'])) ? $data['countryCode'] : null;
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
                'name' => 'agentId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'legId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'countryId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'typeOfPermission',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 4,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'requestTime',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 40,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'permission',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 40,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'comment',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 30,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
