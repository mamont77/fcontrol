<?php

namespace FcLibrariesSearch\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AdvancedSearchForm extends Form
{
    /**
     * @var array
     */
    protected $aircraft_types = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        parent::__construct($name);
        $this->setAircraftTypes($options['aircraft_types']);

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'aircraft_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Type Aircraft',
                'empty_option' => '-- Please select --',
                'value_options' => $this->aircraft_types,
            ),
        ));

        $this->add(array(
            'name' => 'reg_number',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Reg Number',
            ),
        ));

        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'options' => array(
                'primary' => true,
            ),
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));

        //Cancel button
        $this->add(array(
            'name' => 'cancel',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Cancel',
            ),
            'attributes' => array(
                'class' => 'btn-link cancel',
            ),
        ));
    }

    /**
     * @param \Zend\Db\ResultSet\ResultSet $data
     */
    private function setAircraftTypes(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->aircraft_types) {
            foreach ($data as $row) {
                $this->aircraft_types[$row->id] = $row->name;
            }
            uasort($this->aircraft_types, array($this, 'sortLibrary'));
        }
    }

    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
