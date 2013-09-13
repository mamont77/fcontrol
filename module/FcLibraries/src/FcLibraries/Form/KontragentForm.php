<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class KontragentForm
 * @package FcLibraries\Form
 */
class KontragentForm extends Form
{
    protected $_formName = 'kontragent';

    public function __construct($name = null)
    {
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
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Name of Kontragent',
            ),
        ));

        $this->add(array(
            'name' => 'short_name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '15',
            ),
            'options' => array(
                'label' => 'Short name of Kontragent',
            ),
        ));

        $this->add(array(
            'name' => 'address',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'required' => true,
                'rows' => 5,
                'maxlength' => '50',
            ),
            'options' => array(
                'label' => 'Address',
            ),
        ));

        $this->add(array(
            'name' => 'phone1',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Phone 1',
            ),
        ));

        $this->add(array(
            'name' => 'phone2',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Phone 2',
            ),
        ));

        $this->add(array(
            'name' => 'phone3',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Phone 3',
            ),
        ));

        $this->add(array(
            'name' => 'fax',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Fax',
            ),
        ));

        $this->add(array(
            'name' => 'mail',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'E-mail',
            ),
        ));

        $this->add(array(
            'name' => 'sita',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'SITA',
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
