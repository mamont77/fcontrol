<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;


class RegionForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('region');

        $this->setName('region');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        //Text
        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Region of the world',
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Region',
            ),
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));


        //Plain button
        $this->add(array(
            'name'      => 'cancel',
            'type'      => 'Zend\Form\Element\Button',
            'options'   => array(
               'label'      => 'Cancel',
            ),
        ));

    }
}
