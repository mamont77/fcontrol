<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Element;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class RefuelForm
 * @package FcFlight\Form
 */
class RefuelForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'leg';

    /**
     * @var array
     */
    protected $_airports = array();

    /**
     * @var array
     */
    protected $_agents = array();

    /**
     * @var array
     */
    protected $_units = array();

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

        $this->_airports = $options['libraries']['airports'];
        $this->setLibrary('_agents', $options['libraries']['agents'], 'id', 'name');
        $this->setLibrary('_units', $options['libraries']['units'], 'id', 'name');

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'headerId',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $options['headerId'],
            ),
        ));

//        $this->add(array(
//            'name' => 'previousDate',
//            'attributes' => array(
//                'type' => 'hidden',
//                'value' => $options['previousValues']['previousDate'],
//            ),
//        ));

        $this->add(array(
            'name' => 'airportId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'airportId',
                'class' => 'chosen input-large',
                'data-placeholder' => 'Airport',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_airports,
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'date',
                'class' => 'input-small',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Date',
                'min' => '',
                'max' => '',
            ),
            'options' => array(
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'agentId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'agentId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Agent',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_agents,
            ),
        ));

        $this->add(array(
            'name' => 'quantityLtr',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'quantityLtr',
                'class' => 'input-small',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Quantity LTR',
            ),
        ));

        $this->add(array(
            'name' => 'quantityOtherUnits',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'quantityOtherUnits',
                'class' => 'input-small',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Quantity Other Units',
            ),
        ));

        $this->add(array(
            'name' => 'unitId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'unitId',
                'class' => 'chosen input-small',
                'data-placeholder' => 'Unit',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_units,
            ),
        ));

        $this->add(array(
            'name' => 'priceUsd',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'priceUsd',
                'class' => 'input-small',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Price, USD',
            ),
        ));

        $this->add(array(
            'name' => 'totalPriceUsd',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'totalPriceUsd',
                'class' => 'input-small',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Total, USD',
                'readonly' => true,
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-small',
                'required' => false,
                'placeholder' => 'Comment',
                'maxlength' => '30',
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Status',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'description' => 'In process OR Done.',
            ),
            'attributes' => array(
                'value' => false,
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
                'class' => 'btn btn-primary',
                'value' => 'Add',
            ),
        ));
    }
}
