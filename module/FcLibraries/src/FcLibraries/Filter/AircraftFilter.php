<?php
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class AircraftFilter extends BaseFilter
{
    /**
     * @var string
     */
    protected $table = 'library_aircraft';

    public $id;
    public $aircraft_type;
    public $aircraft_type_name;
    public $reg_number;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->aircraft_type = (isset($data['aircraft_type'])) ? $data['aircraft_type'] : null;
        $this->aircraft_type_name = (isset($data['aircraft_type_name'])) ? $data['aircraft_type_name'] : null;
        $this->reg_number = (isset($data['reg_number'])) ? $data['reg_number'] : null;
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
                    array(
                        'name' => 'Digits',
                    ),
                    $this->_noRecordExistsValidators($this->table, 'reg_number', $this->id),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
