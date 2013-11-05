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
            'name' => 'agentsList',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'agentsList',
                'class' => 'typeahead input-small',
                'required' => true,
                'placeholder' => 'Agent',
            ),
            'options' => array(
                'label' => 'Agent',
            ),
        ));

        $this->add(array(
            'name' => 'agentId',
            'attributes' => array(
                'id' => 'agentId',
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'legsList',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'legsList',
                'class' => 'typeahead input-small',
                'required' => true,
                'placeholder' => 'LEG',
            ),
            'options' => array(
                'label' => 'LEG',
            ),
        ));

        $this->add(array(
            'name' => 'legId',
            'attributes' => array(
                'id' => 'legId',
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'countriesList',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'countriesList',
                'class' => 'typeahead input-small',
                'required' => true,
                'placeholder' => 'Country',
            ),
            'options' => array(
                'label' => 'Country',
            ),
        ));

        $this->add(array(
            'name' => 'countryId',
            'attributes' => array(
                'id' => 'countryId',
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'typeOfPermissionsList',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'typeOfPermissionsList',
                'class' => 'typeahead input-small',
                'required' => true,
                'placeholder' => 'Type of permission',
            ),
            'options' => array(
                'label' => 'Type of permission',
            ),
        ));

        $this->add(array(
            'name' => 'typeOfPermission',
            'attributes' => array(
                'id' => 'typeOfPermission',
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'permission',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-small',
                'required' => true,
                'placeholder' => 'Permission',
                'maxlength' => '40',
            ),
            'options' => array(
                'label' => 'Permission',
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
