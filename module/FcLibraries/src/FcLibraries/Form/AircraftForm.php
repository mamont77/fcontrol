<?php
/**
 * @namespace
 */
namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class AircraftForm
 * @package FcLibraries\Form
 */
class AircraftForm extends Form
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
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (is_array($value) && count($value) == 1) {
            $key = key($value);
            $this->{$name}[$key] = $value[$key];
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if ($this->__isset($name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param \Zend\Db\ResultSet\ResultSet $data
     */
    private function setAircraftTypes(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->__get('aircraft_types')) {
            foreach ($data as $row) {
                $this->__set('aircraft_types', array($row->id => $row->name));
            }
            $aircraftTypes = $this->__get('aircraft_types');
            uasort($aircraftTypes, array($this, 'sortLibrary'));
        }
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
