<?php
/**
 * @namespace
 */
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class BaseFilter
 * @package FcLibraries\Filter
 */
class BaseFilter implements InputFilterAwareInterface
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
    public $name;

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
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
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
        $this->name = (isset($data['name'])) ? $data['name'] : null;
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
                'name' => 'name',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param $tableName
     * @param $fieldName
     * @param null $objectId
     * @return array
     */
    protected function _noRecordExistsValidators($tableName, $fieldName, $elementId = null)
    {
        if (is_numeric($elementId)) {
            return array(
                'name' => 'Db\NoRecordExists',
                'options' => array(
                    'table' => $tableName,
                    'field' => $fieldName,
                    'exclude' => array(
                        'field' => 'id',
                        'value' => $elementId
                    ),
                    'adapter' => $this->getDbAdapter(),
                ),
            );
        } else {
            return array(
                'name' => 'Db\NoRecordExists',
                'options' => array(
                    'table' => $tableName,
                    'field' => $fieldName,
                    'adapter' => $this->getDbAdapter(),
                ),
            );
        }
    }
}
