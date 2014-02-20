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
 * Class AircraftFilter
 * @package FcLibraries\Filter
 */
class AircraftFilter extends BaseFilter
{
    /**
     * @var string
     */
    protected $table = 'library_aircraft';

    public $id = null;
    public $aircraft_type = null;
    public $aircraft_type_name = null;
    public $reg_number = null;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        if (isset($data['id'])) {
            $this->__set('id', $data['id']);
        }
        if (isset($data['aircraft_type'])) {
            $this->__set('aircraft_type', $data['aircraft_type']);
        }
        if (isset($data['aircraft_type_name'])) {
            $this->__set('aircraft_type_name', $data['aircraft_type_name']);
        }
        if (isset($data['reg_number'])) {
            $this->__set('reg_number', $data['reg_number']);
        }
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
                'name' => 'aircraft_type',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'reg_number',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'reg_number', $this->id),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
