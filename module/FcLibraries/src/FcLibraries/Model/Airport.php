<?php
namespace FcLibraries\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use FcLibraries\Model\LibraryModel;

class Airport extends LibraryModel
{
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
        if (!$this->_inputFilter) {
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
                'filters' => $this->_filters,
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
                'filters' => $this->_filters,
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
                'filters' => $this->_filters,
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
                'filters' => $this->_filters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 3,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'country',
                'required' => true,
            )));

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
