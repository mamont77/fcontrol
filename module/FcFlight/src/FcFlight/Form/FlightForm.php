<?php

namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class FlightForm extends Form
{
    protected $_formName = 'flight';

    /**
     * @var array
     */
    protected $kontragents = array();

    /**
     * @var array
     */
    protected $airOperators = array();

    /**
     * @var array
     */
    protected $aircrafts = array();


    public function __construct($name = null, $options)
    {
        if (!is_null($name)) {
            $this->_formName = $name;
        }

        parent::__construct($this->_formName);

        $this->setLibrary('kontragents', 'id', 'name', $options['libraries']['kontragent']);
        $this->setLibrary('airOperators', 'id', 'short_name', $options['libraries']['air_operator']); //don't rename
        $this->setLibrary('aircrafts', 'reg_number', 'aircraft_type', $options['libraries']['aircraft']);

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'refNumberOrder',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'dateOrder', //TODO fix date format
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Date Order',
                'description' => 'YYYY-MM-DD',
            ),
        ));

        $this->add(array(
            'name' => 'kontragent',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Customer',
                'empty_option' => '-- Please select --',
                'value_options' => $this->kontragents,
            ),
        ));

        $this->add(array(
            'name' => 'airOperator',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Air Operator',
                'empty_option' => '-- Please select --',
                'value_options' => $this->airOperators,
            ),
        ));

        $this->add(array(
            'name' => 'aircraft',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Aircraft Type',
                'empty_option' => '-- Please select --',
                'value_options' => $this->aircrafts,
                'hint' => 'GegNumber',

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
     * @param $name
     * @param \Zend\Db\ResultSet\ResultSet $data
     * @return FlightForm
     */
    private function setLibrary($LibraryName, $baseFieldKey = 'id', $baseFieldName, \Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->{$LibraryName}) {
            foreach ($data as $row) {
                $this->{$LibraryName}[$row->{$baseFieldKey}] = $row->{$baseFieldName};
            }
        }
        return $this;
    }
}
