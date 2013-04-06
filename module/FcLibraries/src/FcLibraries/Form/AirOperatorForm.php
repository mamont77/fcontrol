<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;


class AirOperatorForm extends Form
{
    protected $_formName = 'air_operator';

    /**
     * @var array
     */
    protected $countries = array();

    public function __construct($name = null, $options)
    {
        parent::__construct($this->_formName);
        $this->setCountries($options['countries']);

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Name of Air Operator',
            ),
        ));

        $this->add(array(
            'name' => 'short_name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '15',
            ),
            'options' => array(
                'label' => 'Short name of Air Operator',
            ),
        ));

        $this->add(array(
            'name' => 'code_icao',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'minlength' => '3',
                'maxlength' => '3',
            ),
            'options' => array(
                'label' => 'Operator code ICAO',
            ),
        ));

        $this->add(array(
            'name' => 'code_iata',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'minlength' => '2',
                'maxlength' => '2',
            ),
            'options' => array(
                'label' => 'Operator code IATA',
            ),
        ));

        $this->add(array(
            'name' => 'country',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Country',
                'empty_option' => '-- Please select --',
                'value_options' => $this->countries,
            ),
        ));

        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));
    }

    /**
     * @param \Zend\Db\ResultSet\ResultSet $data
     */
    private function setCountries(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->countries) {
            foreach ($data as $row) {
                $this->countries[$row->id] = $row->name;
            }
        }
    }
}