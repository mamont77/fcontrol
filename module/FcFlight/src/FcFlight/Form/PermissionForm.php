<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class PermissionForm
 * @package FcFlight\Form
 */
class PermissionForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'permission';

    /**
     * @var array
     */
    protected $agents = array();

    /**
     * @var array
     */
    protected $airportsApDep = array();

    /**
     * @var array
     */
    protected $airportsApArr = array();

    /**
     * @var array
     */
    protected $airports = array();

    protected $baseOfPermit = array();

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

        $this->setLibrary('agents', $options['libraries']['agents'], 'id',
            array('name', 'countryName'), 'object');

        $this->setLibrary('airportsApDep', $options['libraries']['airports'], 'apDepAirportId',
            array('apDepIata', 'apDepIcao'), 'array');
        $this->setLibrary('airportsApArr', $options['libraries']['airports'], 'apArrAirportId',
            array('apArrIata', 'apArrIcao'), 'array');
        $this->setAirports($this->airportsApDep, $this->airportsApArr);

        $this->setLibrary('baseOfPermit', $options['libraries']['baseOfPermit'], 'id',
            array('cityName', 'countryName'), 'object');

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
            'name' => 'agentId',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'agentId',
                'class' => 'typeahead',
                'required' => true,
                'placeholder' => 'Agent',
            ),
            'options' => array(
                'label' => 'Agent',
            ),
        ));

        $this->add(array(
            'name' => 'legId',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '30',
            ),
            'options' => array(
                'label' => 'LEG',
            ),
        ));

        $this->add(array(
            'name' => 'countryId',
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
            'name' => 'typeOfPermission',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Type of permission',
                'value_options' => array(
                    'OFL' => 'OFL',
                    'LND' => 'LND',
                    'DG' => 'DG',
                    'DIP' => 'DIP',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'permission',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'required' => true,
                'rows' => 5,
                'maxlength' => '400',
            ),
            'options' => array(
                'label' => 'Permission',
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
                'class' => 'btn btn-primary',
                'value' => 'Add',
            ),
        ));
    }

    /**
     * @param array $a
     * @param array $b
     */
    public function setAirports(array $a, array $b)
    {
        $compare = $a + $b;
        uasort($compare, array($this, 'sortLibrary'));
        $this->airports = $compare;
    }

    /**
     * @return array
     */
    public function getAgents()
    {
        return $this->agents;
    }

    /**
     * @return array
     */
    public function getAirports()
    {
        return $this->airports;
    }


    /**
     * @return array
     */
    public function getBaseOfPermit()
    {
        return $this->baseOfPermit;
    }
}
