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
    protected $_airportsApDep = array();

    /**
     * @var array
     */
    protected $_airportsApArr = array();

    /**
     * @var array
     */
    protected $_agents = array();

    /**
     * @var array
     */
    protected $_legs = array();

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

        $this->setLibrary('_agents', $options['libraries']['agents'], 'id', 'name');
        $this->_legs = $options['libraries']['legs'];
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

        $this->add(array(
            'name' => 'previousDate',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $options['previousValues']['previousDate'],
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
            'name' => 'legId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'legId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'LEG',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_legs,
            ),
        ));

        $this->add(array(
            'name' => 'quantity',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'quantity',
                'class' => 'input-medium',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Quantity',
            ),
        ));

        $this->add(array(
            'name' => 'unitId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'unitId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Unit',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_units,
            ),
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'price',
                'class' => 'input-medium',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Price',
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'date',
                'class' => 'input-medium',
                'required' => true,
                'maxlength' => '10',
                'placeholder' => 'Date',
            ),
            'options' => array(
                'description' => 'DD-MM-YYYY',
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
