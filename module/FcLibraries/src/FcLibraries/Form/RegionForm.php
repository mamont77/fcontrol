<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Validator\AbstractValidator;

class RegionForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('region');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'form-horizontal',
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Войти',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}
