<?php

namespace FcAdmin\Form;

use Zend\Form\Form;
use Zend\Validator\AbstractValidator;

class UserForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('user');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'form-horizontal',
        ));

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text',
                'required' => true,
                'minlength' => '6',
                'maxlength' => '255',
            ),
            'options' => array(
                'label' => 'Фамилия Имя',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'required' => true,
                'maxlength' => '255',
                'class' => 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'type' => 'Select',
            'name' => 'role_id',
            'options' => array(
                'label' => 'Роль',
            ),
            'attributes' => array(
                'required' => true,
                'options' => array(
                    'user' => 'user',
                    'manager' => 'manager',
                    'admin' => 'admin',
                ),
                'value' => 'user',
            )
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
            'type' => 'password',
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'required' => true,
            ),
            'options' => array(
                'label' => 'Пароль',
            ),
        ));

        $this->add(array(
            'name' => 'passwordVerify',
            'options' => array(
                'label' => 'Повторить пароль',
            ),
            'attributes' => array(
                'type' => 'password',
                'required' => true,
            ),
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
