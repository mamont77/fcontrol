<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class PermissionForm
 * @package FcFlight\Form
 */
class PermissionForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'permission';

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
    protected $_countries = array();

    /**
     * @var array
     */
    protected $_typeOfPermissions = array();

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
        $this->setLibrary('_countries', $options['libraries']['countries'], 'id', 'name');
        $this->_typeOfPermissions = $options['libraries']['typeOfPermissions'];

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
                'id' => 'headerId',
                'type' => 'hidden',
                'value' => $options['headerId'],
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
            'name' => 'countryId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'countryId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Country',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_countries,
            ),
        ));


        $this->add(array(
            'name' => 'typeOfPermission',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'typeOfPermission',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Type of permission',
                'required' => true,
            ),
            'options' => array(
                'empty_option' => '',
                'value_options' => $this->_typeOfPermissions,
            ),
        ));

        $this->add(array(
            'name' => 'permission',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
                'required' => true,
                'placeholder' => 'Permission',
                'maxlength' => '40',
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
                'required' => true,
                'placeholder' => 'Comment',
                'maxlength' => '30',
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
