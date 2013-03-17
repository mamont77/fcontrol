<?php

namespace FcAdmin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UserForm extends Form
{

    public function __construct()
    {
        parent::__construct();

        $this->setName('user');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Фамилия Имя',
            ),
            'attributes' => array(
                'required' => true,
                'minlength' => '6',
                'maxlength' => '255',
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'required' => true,
                'maxlength' => '255',
            ),
        ));

//        $this->add(array(
//            'name' => 'role_id',
//            'type' => 'Select',
//            'options' => array(
//                'label' => 'Роль',
//            ),
//            'attributes' => array(
//                'required' => true,
//                'options' => array(
//                    'user' => 'user',
//                    'manager' => 'manager',
//                    'admin' => 'admin',
//                ),
//                'value' => 'user',
//            )
//        ));

        $this->add(array(
            'name' => 'role_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Роль',
                'value_options' => array(
                    'user' => 'user',
                    'manager' => 'manager',
                    'admin' => 'admin',
                ),
            ),
            'value' => 'user',
        ));

        $this->add(array(
            'name' => 'state',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Статус',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'required' => true,
            ),
            'options' => array(
                'label' => 'Пароль',
            ),
        ));

        $this->add(array(
            'name' => 'passwordVerify',
            'type' => 'password',
            'options' => array(
                'label' => 'Повторить пароль',
            ),
            'attributes' => array(
                'required' => true,
            ),
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Добавить',
            ),
            'options' => array(
                'primary' => true,
            ),
        ));

        //Reset button
        $this->add(array(
            'name' => 'resetBtn',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'Сброс',
            ),
        ));

    }
}
