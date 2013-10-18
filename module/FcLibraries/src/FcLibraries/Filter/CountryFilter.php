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
 * Class CountryFilter
 * @package FcLibraries\Filter
 */
class CountryFilter extends BaseFilter
{
    /**
     * @var string
     */
    protected $table = 'library_country';

    public $id;
    public $name;
    public $region_id;
    public $region_name;
    public $code;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->region_id = (isset($data['region_id'])) ? $data['region_id'] : null;
        $this->region_name = (isset($data['region_name'])) ? $data['region_name'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
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
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 30,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'name', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'region_id',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'code',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 3,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'code', $this->id),

                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
