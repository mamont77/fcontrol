<?php

namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class FlightDataForm extends Form
{
    protected $_formName = 'flightData';

    /**
     * @var array
     */
    protected $parentFormId = array();

    /**
     * @var array
     */
    protected $flightNumberIds = array();

    /**
     * @var array
     */
    protected $apDepIds = array();

    /**
     * @var array
     */
    protected $apArrIds = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        if (!is_null($name)) {
            $this->_formName = $name;
        }

        parent::__construct($this->_formName);

        $this->setLibrary('airOperators', 'id', 'code_icao', $options['libraries']['air_operator']); //don't rename
        $this->setLibrary('airports', 'reg_number', 'code_icao', $options['libraries']['aircraft']);

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'parentFormId',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'dateOfFlight',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Date Of Flight',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'flightNumberId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Air Operators',
                'empty_option' => '-- Please select --',
                'value_options' => $this->flightNumberIds,
            ),
        ));

        $this->add(array(
            'name' => 'flightNumberText',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '6',
            ),
            'options' => array(
                'label' => 'Flight Number',
            ),
        ));

        $this->add(array(
            'name' => 'apDepId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Airports',
                'empty_option' => '-- Please select --',
                'value_options' => $this->apDepIds,
            ),
        ));

        $this->add(array(
            'name' => 'apDepTime',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '5',
            ),
            'options' => array(
                'label' => 'Time, UTC',
                'hint' => 'HH:MM',
                'description' => 'UTC',
            ),
        ));

        $this->add(array(
            'name' => 'apArrId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Airports',
                'empty_option' => '-- Please select --',
                'value_options' => $this->apDepIds,
            ),
        ));

        $this->add(array(
            'name' => 'apArrTime',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '5',
            ),
            'options' => array(
                'label' => 'Time, UTC',
                'hint' => 'HH:MM',
                'description' => 'UTC',
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
     * @param $LibraryName
     * @param string $baseFieldKey
     * @param $baseFieldName
     * @param \Zend\Db\ResultSet\ResultSet $data
     * @return FlightHeaderForm
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
