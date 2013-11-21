<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class FlightHeaderForm
 * @package FcFlight\Form
 */
class FlightHeaderForm extends BaseForm
{
    protected $_formName = 'flightHeader';

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

        $this->setLibrary('kontragents', $options['libraries']['kontragent'], 'id', 'name');
        $this->setLibrary('airOperators', $options['libraries']['air_operator'], 'id', 'short_name'); //don't rename
        $this->setLibrary('aircrafts', $options['libraries']['aircraft'], 'reg_number', 'aircraft_type_name');

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
            'name' => 'dateOrder',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
                'value' => date('d-m-Y'),
            ),
            'options' => array(
                'label' => 'Date Order',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'kontragent',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
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
                'size' => 5,
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
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Aircraft Type',
                'empty_option' => '-- Please select --',
                'value_options' => $this->aircrafts,
                'hint' => 'GegNumber',
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Status',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'description' => 'In process (active) OR Done.',
            ),
            'attributes' => array(
                'value' => true,
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
}
