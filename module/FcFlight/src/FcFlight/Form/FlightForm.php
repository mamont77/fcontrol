<?php

namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class FlightForm extends Form
{
    protected $_formName = 'flight';

    /**
     * @var array
     */
    protected $kontragents = array();

    /**
     * @var array
     */
    protected $airOperators = array();

    /**
     * @var array
     */
    protected $aircrafts = array();


    public function __construct($name = null, $options)
    {
        if (!is_null($name)) {
            $this->_formName = $name;
        }

        parent::__construct($this->_formName);

        $this->setLibrary('kontragents', 'name', $options['libraries']['kontragent']);
        $this->setLibrary('airOperators', 'name', $options['libraries']['air_operator']);
        $this->setLibrary('aircrafts', 'reg_number', $options['libraries']['aircraft']);

//        echo'<pre>kontragents</pre>';
//        $temp = $this->kontragents;
//        foreach ($temp as $k => $i) {
//            echo'<pre>';var_dump($i);echo'</pre>';
//
//        }

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
                'label' => 'Region of the world',
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
     * @param $name
     * @param \Zend\Db\ResultSet\ResultSet $data
     * @return FlightForm
     */
    private function setLibrary($LibraryName, $baseFieldName, \Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->{$LibraryName}) {
            foreach ($data as $row) {
                $this->{$LibraryName}[$row->id] = $row->{$baseFieldName};
            }
        }
        return $this;
    }
}
