<?php
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class AirportFilter extends BaseFilter
{
    /**
     * @var string
     */
    protected $table = 'library_airport';

    public $id;
    public $name;
    public $short_name;
    public $code_icao;
    public $code_iata;
    public $country;
    public $country_name;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->short_name = (isset($data['short_name'])) ? $data['short_name'] : null;
        $this->code_icao = (isset($data['code_icao'])) ? $data['code_icao'] : null;
        $this->code_iata = (isset($data['code_iata'])) ? $data['code_iata'] : null;
        $this->country = (isset($data['country'])) ? $data['country'] : null;
        $this->country_name = (isset($data['country_name'])) ? $data['country_name'] : null;
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
                'name' => 'short_name',
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
                    $this->_noRecordExistsValidators($this->table, 'short_name', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'code_icao',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 4,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'code_icao', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'code_iata',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 3,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'code_iata', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'country',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
