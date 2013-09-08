<?php

namespace FcLibrariesSearch\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SearchForm extends Form
{
    /**
     * @var array
     */
    protected $aircraft_types = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setName('search');
        $this->setAttributes(array(
                'method' => 'post',
                'action' => '/admin/libraries/advanced_search',
            ));

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

        $this->add(array(
            'name' => 'library',
            'attributes' => array(
                'type' => 'hidden',
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


    }
}
