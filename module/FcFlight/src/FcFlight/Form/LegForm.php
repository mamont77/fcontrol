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
    protected $airOperators = array();

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

        $this->setLibrary('airOperators', $options['libraries']['airOperators'],
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
            'name' => 'flightNumber',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '8',
                'id' => 'flightNumber',
                'class' => 'input-mini',
                'placeholder' => 'Flight #',
            ),
            'options' => array(
                'label' => 'Flight #',
            ),
        ));

        $this->add(array(
            'name' => 'apDepTime',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'apDepTime',
                'required' => true,
                'maxlength' => '16',
                'class' => 'input-medium',
                'placeholder' => 'Date Dep',
            ),
            'options' => array(
                'label' => 'Date Dep',
                'hint' => '(UTC)',
                'description' => 'DD-MM-YYYY HH:MM',
            ),
        ));

        $this->add(array(
            'name' => 'apDepCountryId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'id' => 'apDepCountryId',
                'class' => 'chosen input-medium noAllowSingleDeselect',
                'data-placeholder' => 'Country Dep',
            ),
            'options' => array(
                'label' => 'Country Dep',
                'empty_option' => '',
                'value_options' => $this->countries,
            ),
        ));

        $this->add(array(
            'name' => 'apDepAirports',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'id' => 'apDepAirports',
                'class' => 'chosen input-small noAllowSingleDeselect',
                'data-placeholder' => 'Airport Dep',
                'disabled' => true,
            ),
            'options' => array(
                'label' => 'Airport Dep',
                'empty_option' => '',
                'value_options' => array(),
            ),
        ));

        $this->add(array(
            'name' => 'apArrTime',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '16',
                'id' => 'apArrTime',
                'class' => 'input-medium',
                'placeholder' => 'Date Arr',
            ),
            'options' => array(
                'label' => 'Date Arr',
                'hint' => '(UTC)',
                'description' => 'DD-MM-YYYY HH:MM',
            ),
        ));

        $this->add(array(
            'name' => 'apArrCountryId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'id' => 'apArrCountryId',
                'class' => 'chosen input-medium',
                'data-placeholder' => 'Country Arr',
            ),
            'options' => array(
                'label' => 'Country Arr',
                'empty_option' => '',
                'value_options' => $this->countries,
            ),
        ));

        $this->add(array(
            'name' => 'apArrAirports',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'id' => 'apArrAirports',
                'class' => 'chosen input-small',
                'data-placeholder' => 'Airport Arr',
                'disabled' => true,
            ),
            'options' => array(
                'label' => 'Airport Arr',
                'empty_option' => '',
                'value_options' => array(),
            ),
        ));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'options' => array(
                'primary' => true,
            ),
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Add',
            ),
        ));
    }
}
