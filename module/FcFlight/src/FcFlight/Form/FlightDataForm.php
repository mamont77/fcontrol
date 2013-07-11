<?php

namespace FcFlight\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class FlightDataForm extends Form
{
    /**
     * @var string
     */
    protected $_formName = 'flightData';

    /**
     * @var array
     */
    protected $flightNumberIdsIcao = array();

    /**
     * @var array
     */
    protected $flightNumberIdsIata = array();

    /**
     * @var array
     */
    protected $apDepIdsIcao = array();

    /**
     * @var array
     */
    protected $apDepIdsIata = array();

    /**
     * @var array
     */
    protected $apArrIdsIcao = array();

    /**
     * @var array
     */
    protected $apArrIdsIata = array();

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

        $this->setLibrary('flightNumberIdsIcao', 'id', 'code_icao', $options['libraries']['flightNumberIds']);
        $this->setLibrary('flightNumberIdsIata', 'id', 'code_iata', $options['libraries']['flightNumberIds']);
        $this->setLibrary('apDepIdsIcao', 'id', 'code_icao', $options['libraries']['apDepIds']);
        $this->setLibrary('apDepIdsIata', 'id', 'code_iata', $options['libraries']['apDepIds']);
        $this->setLibrary('apArrIdsIcao', 'id', 'code_icao', $options['libraries']['apArrIds']);
        $this->setLibrary('apArrIdsIata', 'id', 'code_iata', $options['libraries']['apArrIds']);


        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'parentFormId',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $options['parentFormId'],
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
            'name'          => 'flightNumber',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
                'legend'        => 'Flight Number',
            ),
            'elements'      => array(
                //flightNumberIdIcao
                array(
                    'spec' => array(
                        'name' => 'flightNumberIdIcao',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'flightNumberIdIcao',
                        ),
                        'options' => array(
                            'label' => 'ICAO',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->flightNumberIdsIcao,
                        ),
                    ),
                ),
                //flightNumberIdIata
                array(
                    'spec' => array(
                        'name' => 'flightNumberIdIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'id' => 'flightNumberIdIata',
                        ),
                        'options' => array(
                            'label' => 'IATA',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->flightNumberIdsIata,
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
            'name'          => 'apDep',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
                'legend'        => 'App Dep',
            ),
            'elements'      => array(
                //apDepIdIcao
                array(
                    'spec' => array(
                        'name' => 'apDepIdIcao',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apDepIdIcao',
                        ),
                        'options' => array(
                            'label' => 'ICAO',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->apDepIdsIcao,
                        ),
                    ),
                ),
                //apDepIdIata
                array(
                    'spec' => array(
                        'name' => 'apDepIdIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'id' => 'apDepIdIata',
                        ),
                        'options' => array(
                            'label' => 'IATA',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->apDepIdsIata,
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
            'name'          => 'apArr',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
                'legend'        => 'App Arr',
            ),
            'elements'      => array(
                //apArrIdIcao
                array(
                    'spec' => array(
                        'name' => 'apArrIdIcao',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apArrIdIcao',
                        ),
                        'options' => array(
                            'label' => 'ICAO',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->apArrIdsIcao,
                        ),
                    ),
                ),
                //apArrIdIata
                array(
                    'spec' => array(
                        'name' => 'apArrIdIata',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'id' => 'apArrIdIata',
                        ),
                        'options' => array(
                            'label' => 'IATA',
                            'empty_option' => '-- Please select --',
                            'value_options' => $this->apArrIdsIata,
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
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add',
            ),
        ));

    }

    /**
     * @param $LibraryName
     * @param string $baseFieldKey
     * @param $baseFieldName
     * @param \Zend\Db\ResultSet\ResultSet $data
     * @return FlightHeaderForm
     */
    private function setLibrary($LibraryName, $baseFieldKey = 'id', $baseFieldName, \Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->{$LibraryName}) {
            foreach ($data as $row) {
                if ($row->{$baseFieldName} != '') {
                    $this->{$LibraryName}[$row->{$baseFieldKey}] = $row->{$baseFieldName};
                }
            }
        }
        return $this;
    }
}
