<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class BaseOfPermitForm
 * @package FcLibraries\Form
 */
class BaseOfPermitForm extends Form
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
            'name' => 'airportId',
            'attributes' => array(
                'id' => 'airportId',
                'type' => 'hidden',
                'value' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'countryId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Countries',
                'empty_option' => '-- Please select --',
                'value_options' => $this->countries,
            ),
        ));

        $this->add(array(
            'name' => 'termValidity',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '2',
            ),
            'options' => array(
                'label' => 'Term validity',
            ),
        ));

        $this->add(array(
            'name' => 'termToTake',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '2',
            ),
            'options' => array(
                'label' => 'Term to take',
            ),
        ));

        $this->add(array(
            'name' => 'airports',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
                'disabled' => true,
            ),
            'options' => array(
                'label' => 'Airports',
            ),
        ));

        $this->add(array(
            'name' => 'infoToTake',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'required' => true,
                'rows' => 5,
                'maxlength' => '400',
            ),
            'options' => array(
                'label' => 'Info to take',
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
    private function setCountries(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->countries) {
            foreach ($data as $row) {
                $this->countries[$row->id] = $row->name;
            }
            uasort($this->countries, array($this, 'sortLibrary'));
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
