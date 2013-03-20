<?php

namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;
//use FcLibraries\Controller\RegionController;
use Zend\Mvc\Controller\AbstractController;


class CountryForm extends Form
{
    /**
     * @var string
     */
    protected $_formName = 'country';
    protected $_regionTable = null;
    protected $_regions = null;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($this->_formName);
        $this->setRegions();

        echo'<pre>';
        print_r($this->_regions);
        echo'</pre>';
        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'Country',
            ),
        ));

        $this->add(array(
            'name' => 'region',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Region',
                'value_options' => array(
                    'user' => 'User',
                    'manager' => 'Manager',
                    'admin' => 'Admin',
                ),
            ),
            'value' => 'user',
        ));

        $this->add(array(
            'name' => 'code',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '3',
            ),
            'options' => array(
                'label' => 'Code of Country',
            ),
        ));

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
            'name' => 'cancel',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Cancel',
            ),
        ));

    }

    /**
     * @return array|null|object
     */
    private function setRegions()
    {
        if (!$this->_regions) {
            $this->_regions = $this->getRegionTable();
        }
        return $this->_regions;
    }

}
