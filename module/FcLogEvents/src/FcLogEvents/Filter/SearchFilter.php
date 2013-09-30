<?php
namespace FcLogEvents\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class SearchFilter
 * @package FcLogEvents\Filter
 */
class SearchFilter implements InputFilterAwareInterface
{

    /**
     * @var string
     */
    public $dateFrom;

    /**
     * @var string
     */
    public $dateTo;

    /**
     * @var int|string
     */
    public $priority;

    /**
     * @var string
     */
    public $username;

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
        $this->dateFrom = (isset($data['dateFrom'])) ? $data['dateFrom'] : null;
        $this->dateTo = (isset($data['dateTo'])) ? $data['dateTo'] : null;
        $this->priority = (isset($data['priority'])) ? $data['priority'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
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
                                $dateFrom = \DateTime::createFromFormat('d-m-Y', $context['dateFrom']);
                                $dateTo = \DateTime::createFromFormat('d-m-Y', $value);
                                return $dateTo >= $dateFrom;
                            },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'priority',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'username',
                'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
