<?php

namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class SearchForm
 *
 * @package FcFlight\Form
 */
class SearchForm extends BaseForm
{

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setName('flightSearch');
        $this->setAttributes(array(
                'method' => 'post',
                'action' => '/flights/search',
            )
        );

        $this->add(array(
            'name' => 'dateOrderFrom',
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
            'name' => 'dateOrderTo',
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
            'name' => 'status',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '2' => 'ANY',
                    '1' => 'IN PROCESS',
                    '0' => 'DONE',
                ),
            ),
            'attributes' => array(
                'value' => '2'
            )
        ));

        $this->add(array(
            'name' => 'customer',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Customer',
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
