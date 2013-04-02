<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;


class AircraftTypeForm extends Form
{
    protected $_formName = 'aircraft_type';

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
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Type Aircraft',
            ),
        ));

        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));
    }
}
