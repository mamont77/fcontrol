<?php

namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

class LegForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'leg';

    /**
     * @var array
     */
    protected $flightNumberIcaoAndIata = array();

    /**
     * @var array
     */
    protected $appIcaoAndIata = array();


    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options)
    {
        if (!is_null($name)) {
            $this->_formName = $name;
        }

        parent::__construct($this->_formName);

        $this->setLibrary('flightNumberIcaoAndIata', $options['libraries']['flightNumberIcaoAndIata'],
            'id', array('code_icao', 'code_iata'));
        $this->setLibrary('appIcaoAndIata', $options['libraries']['appIcaoAndIata'],
            'id', array('code_iata', 'code_icao'));

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'headerId',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $options['headerId'],
            ),
        ));

        $this->add(array(
            'name' => 'dateOfFlight',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Date Of Flight',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        //Fieldset Flight Number
        $this->add(array(
            'name' => 'flightNumber',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'legend' => 'Flight Number',
            ),
            'elements' => array(
                //flightNumberIcaoAndIata
                array(
                    'spec' => array(
                        'name' => 'flightNumberIcaoAndIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'flightNumberIcaoAndIata',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'ICAO (IATA)',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->flightNumberIcaoAndIata,
                        ),
                    ),
                ),
                //flightNumberText
                array(
                    'spec' => array(
                        'name' => 'flightNumberText',
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => array(
                            'required' => true,
                            'maxlength' => '6',
                            'id' => 'flightNumberText',
                        ),
                        'options' => array(
                            'label' => 'Text',
                        ),
                    ),
                ),
            ),
        ));

        //Fieldset Ap Dep
        $this->add(array(
            'name' => 'apDep',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'legend' => 'App Dep',
            ),
            'elements' => array(
                //apDepIcaoAndIata
                array(
                    'spec' => array(
                        'name' => 'apDepIcaoAndIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apDepIcaoAndIata',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'IATA (ICAO)',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->appIcaoAndIata,
                        ),
                    ),
                ),
                //apDepTime
                array(
                    'spec' => array(
                        'name' => 'apDepTime',
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => array(
                            'required' => true,
                            'maxlength' => '5',
                            'id' => 'apDepTime',
                        ),
                        'options' => array(
                            'label' => 'Time',
                            'hint' => 'HH:MM',
                            'description' => 'UTC',
                        ),
                    ),
                ),
            ),
        ));

        //Fieldset Ap Arr
        $this->add(array(
            'name' => 'apArr',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'legend' => 'App Arr',
            ),
            'elements' => array(
                //apArrIcaoAndIata
                array(
                    'spec' => array(
                        'name' => 'apArrIcaoAndIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apArrIcaoAndIata',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'IATA (ICAO)',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->appIcaoAndIata,
                        ),
                    ),
                ),
                //apArrTime
                array(
                    'spec' => array(
                        'name' => 'apArrTime',
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => array(
                            'required' => true,
                            'maxlength' => '5',
                            'id' => 'apArrTime',
                        ),
                        'options' => array(
                            'label' => 'Time',
                            'hint' => 'HH:MM',
                            'description' => 'UTC',
                        ),
                    ),
                ),
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

    }
}