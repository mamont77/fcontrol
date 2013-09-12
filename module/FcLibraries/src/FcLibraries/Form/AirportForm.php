<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class AirportForm
 * @package FcLibraries\Form
 */
class AirportForm extends Form
{
    /**
     * @var array
     */
    protected $cities = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        parent::__construct($name);
        $this->setCities($options['cities']);

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
                'minlength' => '4',
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
                'minlength' => '3',
                'maxlength' => '3',
            ),
            'options' => array(
                'label' => 'Code IATA',
            ),
        ));

        $this->add(array(
            'name' => 'city_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'City',
                'empty_option' => '-- Please select --',
                'value_options' => $this->cities,
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
    private function setCities(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->cities) {
            foreach ($data as $row) {
                $this->cities[$row->id] = $row->name;
            }
            uasort($this->cities, array($this, 'sortLibrary'));
        }
    }

    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
