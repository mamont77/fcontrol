<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class HandingForm
 * @package FcFlight\Form
 */
class HandingForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'handing';

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

    protected $agents = array();

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

        $this->setLibrary('airportsApDep', $options['libraries']['airports'], 'apDepIcaoAndIata',
            array('apDepIata', 'apDepIcao'), 'array');
        $this->setLibrary('airportsApArr', $options['libraries']['airports'], 'apArrIcaoAndIata',
            array('apArrIata', 'apArrIcao'), 'array');
        $this->setAirports($this->airportsApDep, $this->airportsApArr);

        $this->setLibrary('agents', $options['libraries']['agents'], 'id', 'name');

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
            'name' => 'airportId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Airport',
                'empty_option' => '-- Please select --',
                'value_options' => $this->getAirports(),
            ),
        ));

        $this->add(array(
            'name' => 'isNeed',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Need?',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));

        $this->add(array(
            'name' => 'agentId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Agent',
                'empty_option' => '-- Please select --',
                'value_options' => $this->getAgents(),
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
    public function getAirports()
    {
        return $this->airports;
    }

    /**
     * @return array
     */
    public function getAgents()
    {
        return $this->agents;
    }
}
