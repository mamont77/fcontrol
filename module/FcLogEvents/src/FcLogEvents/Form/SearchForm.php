<?php

namespace FcLogEvents\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class SearchForm
 *
 * @package FcLogEvents\Form
 */
class SearchForm extends Form
{

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setName('logsSearch');
        $this->setAttributes(array(
                'method' => 'post',
                'action' => '/logs/search',
            ));

        $this->add(array(
            'name' => 'dateFrom',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Date from',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'dateTo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Date to',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'priority',
            'options' => array(
                'label' => 'Priority',
                'value_options' => array(
                    '' => 'ANY',
                    '6' => 'Info',
                    '5' => 'Notice',
                    '4' => 'Warring',
                ),
            ),
            'attributes' => array(
                'value' => ''
            )
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'User',
            ),
        ));


        $this->add(array(
            'name' => 'airOperator',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Air Operator',
            ),
        ));

        $this->add(array(
            'name' => 'aircraft',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Aircraft',
            ),
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'type'       => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Find',
            ),
        ));


    }
}
