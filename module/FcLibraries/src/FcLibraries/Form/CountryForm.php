<?php
/**
 * @namespace
 */
namespace FcLibraries\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class CountryForm
 * @package FcLibraries\Form
 */
class CountryForm extends Form
{
    /**
     * @var array
     */
    protected $regions = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        parent::__construct($name);
        $this->setRegions($options['regions']);

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
            'name' => 'region_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Region',
                'empty_option' => '-- Please select --',
                'value_options' => $this->regions,
            ),
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
            'options' => array(
                'primary' => true,
            ),
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));

        //Cancel button
        $this->add(array(
            'name' => 'cancel',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Cancel',
            ),
            'attributes' => array(
                'class' => 'btn-link cancel',
            ),
        ));
    }

    /**
     * @param \Zend\Db\ResultSet\ResultSet $data
     */
    private function setRegions(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->regions) {
            foreach ($data as $row) {
                $this->regions[$row->id] = $row->name;
            }
            uasort($this->regions, array($this, 'sortLibrary'));
        }
    }

    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
