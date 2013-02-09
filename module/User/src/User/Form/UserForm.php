<?php

namespace User\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
                'required' => true,
                'maxlength'  => '255',
            ),
            'options' => array(
                'label' => 'Фамилия Имя',
            ),
        ));
        $this->add(array(
            'name' => 'display_name',
            'attributes' => array(
                'type'  => 'text',
                'required' => true,
                'maxlength'  => '50',

            ),
            'options' => array(
                'label' => 'Отображаемое имя',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
                'required' => true,
                'maxlength'  => '255',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'required' => true,
                'maxlength'  => '24',
            ),
            'options' => array(
                'label' => 'Пароль',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'state',
            'options' => array(
                'label' => 'Активен',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}
