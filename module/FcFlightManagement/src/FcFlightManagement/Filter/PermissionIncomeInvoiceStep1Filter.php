<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class PermissionIncomeInvoiceStep1Filter
 * @package FcFlightManagement\Filter
 */
class PermissionIncomeInvoiceStep1Filter implements InputFilterAwareInterface
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
    public $dateFrom;
    public $dateTo;
    public $aircraftId;
    public $agentId;
    public $airportId;
    public $customerId;
    public $airOperatorId;

    //Fields only for view

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
        $this->dateFrom = (isset($data['dateFrom'])) ? $data['dateFrom'] : null;
        $this->dateTo = (isset($data['dateTo'])) ? $data['dateTo'] : null;
        $this->aircraftId = (isset($data['aircraftId'])) ? $data['aircraftId'] : null;
        $this->agentId = (isset($data['agentId'])) ? $data['agentId'] : null;
        $this->airportId = (isset($data['airportId'])) ? $data['airportId'] : null;
        $this->customerId = (isset($data['customerId'])) ? $data['customerId'] : null;
        $this->airOperatorId = (isset($data['airOperatorId'])) ? $data['airOperatorId'] : null;
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
                'name' => 'dateFrom',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'd-m-Y',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be dd-mm-YYYY',
                                'dateInvalidDate' => 'Invalid date, must be dd-mm-YYYY'
                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'dateTo',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'd-m-Y',
                            'messages' => array(
                                'dateFalseFormat' => 'Invalid date format, must be dd-mm-YYYY',
                                'dateInvalidDate' => 'Invalid date, must be dd-mm-YYYY'
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'The "date to" is less than the "date from"',
                            ),
                            'callback' => function ($value, $context) {
                                    $dateOrderFrom = \DateTime::createFromFormat('d-m-Y', $context['dateFrom']);
                                    $dateOrderTo = \DateTime::createFromFormat('d-m-Y', $value);
                                    return $dateOrderTo >= $dateOrderFrom;
                                },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'aircraftId',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'agentId',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name' => 'airportId',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'customerId',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airOperatorId',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
