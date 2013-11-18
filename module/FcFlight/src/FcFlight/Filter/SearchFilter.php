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
 * Class SearchFilter
 * @package FcFlight\Filter
 */
class SearchFilter implements InputFilterAwareInterface
{
    /**
     * @var $inputFilter
     */
    protected $inputFilter;

    /**
     * @var string
     */
    public $dateOrderFrom;

    /**
     * @var string
     */
    public $dateOrderTo;

    /**
     * @var int|string
     */
    public $status;

    /**
     * @var string
     */
    public $customer;

    /**
     * @var string
     */
    public $airOperator;

    /**
     * @var string
     */
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
        $this->dateOrderFrom = (isset($data['dateOrderFrom'])) ? $data['dateOrderFrom'] : null;
        $this->dateOrderTo = (isset($data['dateOrderTo'])) ? $data['dateOrderTo'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->customer = (isset($data['customer'])) ? $data['customer'] : null;
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
                'name' => 'dateOrderFrom',
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
                'name' => 'dateOrderTo',
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
                                $dateOrderFrom = \DateTime::createFromFormat('d-m-Y', $context['dateOrderFrom']);
                                $dateOrderTo = \DateTime::createFromFormat('d-m-Y', $value);
                                return $dateOrderTo >= $dateOrderFrom;
                            },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'status',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'customer',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 32,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airOperator',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 32,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'aircraft',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 32,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
