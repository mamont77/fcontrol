<?php

namespace FcAdmin\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;

class UserForm extends Form
{

    protected $_translator;


    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');

        $this->_translator = new Translator;
        $this->_translator->addTranslationFile("phparray", './vendor/ZF2/resources/languages/ru/Zend_Validate.php');
        AbstractValidator::setDefaultTranslator($this->_translator);

        $this->setAttributes(array(
            'method' => 'post',
            'class' => '',
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
            'type' => 'password',
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'required' => true,
                'maxlength' => '24',
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
                'type' => 'submit',
                'value' => 'Войти',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}
