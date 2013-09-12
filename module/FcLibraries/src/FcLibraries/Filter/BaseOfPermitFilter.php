<?php
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class BaseOfPermitFilter
 * @package FcLibraries\Filter
 */
class BaseOfPermitFilter extends BaseFilter
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
    public $city_id;
    public $city_name;
    public $country_id;
    public $country_name;
    public $region_name;

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
        $this->city_id = (isset($data['city_id'])) ? $data['city_id'] : null;
        $this->city_name = (isset($data['city_name'])) ? $data['city_name'] : null;
        $this->country_id = (isset($data['country_id'])) ? $data['country_id'] : null;
        $this->country_name = (isset($data['country_name'])) ? $data['country_name'] : null;
        $this->region_name = (isset($data['region_name'])) ? $data['region_name'] : null;
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
                'name' => 'city_id',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
