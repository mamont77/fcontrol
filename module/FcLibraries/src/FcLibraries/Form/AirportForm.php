<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AirportForm extends Form
{
    /**
     * @var array
     */
    protected $countries = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        parent::__construct($name);
        $this->setCountries($options['countries']);

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
                'label' => 'Name of Airport',
            ),
        ));

        $this->add(array(
            'name' => 'short_name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Short Name',
            ),
        ));

        $this->add(array(
            'name' => 'code_icao',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '4',
            ),
            'options' => array(
                'label' => 'Code ICAO',
            ),
        ));

        $this->add(array(
            'name' => 'code_iata',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '3',
            ),
            'options' => array(
                'label' => 'Code IATA',
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
