<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use FcFlight\Form\BaseForm;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class RefuelIncomeInvoiceStep1Form
 * @package FcFlightManagement\Form
 */
class RefuelIncomeInvoiceStep1Form extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = '';

    /**
     * @var array
     */
    protected $_aircrafts = array();

    /**
     * @var array
     */
    protected $_agents = array();

    /**
     * @var array
     */
    protected $_airports = array();

    /**
     * @var array
     */
    protected $_customers = array();

    /**
     * @var array
     */
    protected $_airOperators = array();

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

        $this->setName($this->_formName);
        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->setLibrary('_aircrafts', $options['libraries']['aircrafts'],
            'id', array('aircraft_type_name', 'reg_number'));
        $this->setLibrary('_agents', $options['libraries']['agents'], 'id', 'name');
        $this->setLibrary('_airports', $options['libraries']['airports'], 'id', array('code_icao', 'code_iata'));
        $this->setLibrary('_customers', $options['libraries']['customers'], 'id', 'name');
        $this->setLibrary('_airOperators', $options['libraries']['airOperators'], 'id', 'name');

        $this->add(array(
            'name' => 'dateOrderFrom',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dateOrderFrom',
                'class' => 'input-small',
                'required' => false,
                'maxlength' => '10',
                'placeholder' => 'Date From',
            ),
            'options' => array(
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'dateOrderTo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dateOrderTo',
                'class' => 'input-small',
                'required' => false,
                'maxlength' => '10',
                'placeholder' => 'Date To',
            ),
            'options' => array(
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'aircraftId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'aircraftId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Aircraft',
                'required' => false,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_aircrafts,
            ),
        ));

        $this->add(array(
            'name' => 'agentId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'agentId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Fuel supplier',
                'required' => false,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_agents,
            ),
        ));

        $this->add(array(
            'name' => 'airportId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'airportId',
                'class' => 'chosen input-small',
                'data-placeholder' => 'Airport',
                'required' => false,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_airports,
            ),
        ));

        $this->add(array(
            'name' => 'customerId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'customerId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Customer',
                'required' => false,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_customers,
            ),
        ));

        $this->add(array(
            'name' => 'airOperatorId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'airOperatorId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Air Operator',
                'required' => false,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_airOperators,
            ),
        ));

//        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'options' => array(
                'primary' => true,
            ),
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Apply',
            ),
        ));
    }
}
