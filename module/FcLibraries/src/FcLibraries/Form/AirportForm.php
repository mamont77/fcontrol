<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;


class AirportForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setName('demoFormInline');
        $this->setAttribute('method', 'post');

        //Text
        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Search term...',
            ),
            'options' => array(
                'label' => 'Text',
            ),
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}
