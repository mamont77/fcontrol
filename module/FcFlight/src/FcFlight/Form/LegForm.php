<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class LegForm
 * @package FcFlight\Form
 */
class LegForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'leg';

    /**
     * @var array
     */
    protected $flightNumberAirports = array();

    /**
     * @var array
     */
    protected $countries = array();

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

        $this->setLibrary('flightNumberAirports', $options['libraries']['flightNumberAirports'],
            'id', array('code_icao', 'code_iata'));
        $this->setLibrary('countries', $options['libraries']['countries'], 'id', 'name');

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

        // используется при валидации в FlightDateChecker
        $this->add(array(
            'name' => 'previousDate',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $options['previousValues']['previousDate'],
            ),
        ));

        // необходим, что бы при добавлении очередной строки выбрать Country в Ap Dep
        // после чего заблокировать это поле для редактирования
        $this->add(array(
            'name' => 'preSelectedApDepCountryId',
            'attributes' => array(
                'id' => 'preSelectedApDepCountryId',
                'type' => 'hidden',
                'value' => $options['previousValues']['preSelected']['apDepCountryId'],
            ),
        ));

        // необходим, что бы при добавлении очередной строки выбрать Airport (IATA (ICAO)) в Ap Dep
        // после чего заблокировать это поле для редактирования
        $this->add(array(
            'name' => 'preSelectedApDepAirportId',
            'attributes' => array(
                'id' => 'preSelectedApDepAirportId',
                'type' => 'hidden',
                'value' => $options['previousValues']['preSelected']['apDepAirportId'],
            ),
        ));

        // эти данные будут записаны в БД, а также участвуют в рендеринге формы после того как ajax возмет аэропорты
        $this->add(array(
            'name' => 'apDepAirportId',
            'attributes' => array(
                'id' => 'apDepAirportId',
                'type' => 'hidden',
                'value' => 0,
            ),
        ));

        // эти данные будут записаны в БД, а также участвуют в рендеринге формы после того как ajax возмет аэропорты
        $this->add(array(
            'name' => 'apArrAirportId',
            'attributes' => array(
                'id' => 'apArrAirportId',
                'type' => 'hidden',
                'value' => 0,
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
                //flightNumberAirportId
                array(
                    'spec' => array(
                        'name' => 'flightNumberAirportId',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'flightNumberAirportId',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'ICAO (IATA)',
                            'empty_option' => '-- Select --',
                            'value_options' => $this->flightNumberAirports,
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
                'legend' => 'Ap Dep',
            ),
            'elements' => array(
                array(
                    'spec' => array(
                        'name' => 'apDepCountries',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apDepCountries',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'Country',
                            'empty_option' => '-- Select --',
                            'value_options' => $this->countries,
                        ),
                    ),
                ),
                //apDepIcaoAndIata
                array(
                    'spec' => array(
                        'name' => 'apDepAirports',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apDepAirports',
                            'size' => 5,
                            'disabled' => true,
                        ),
                        'options' => array(
                            'label' => 'IATA (ICAO)',
//                            'description' => ' ',
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
                'legend' => 'Ap Arr',
            ),
            'elements' => array(
                array(
                    'spec' => array(
                        'name' => 'apArrCountries',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apArrCountries',
                            'size' => 5,
                        ),
                        'options' => array(
                            'label' => 'Country',
                            'empty_option' => '-- Select --',
                            'value_options' => $this->countries,
                        ),
                    ),
                ),
                //apArrIcaoAndIata
                array(
                    'spec' => array(
                        'name' => 'apArrAirports',
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'required' => true,
                            'id' => 'apArrAirports',
                            'size' => 5,
                            'disabled' => true,
                        ),
                        'options' => array(
                            'label' => 'IATA (ICAO)',
//                            'description' => ' ',
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

//        $this->add(new Element\Csrf('csrf'));

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
