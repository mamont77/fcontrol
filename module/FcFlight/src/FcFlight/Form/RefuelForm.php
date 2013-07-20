<?php

namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

class RefuelForm extends Form
{
    /**
     * @var string
     */
    protected $_formName = 'flightData';

    /**
     * @var array
     */
    protected $airports = array();

    /**
     * @var array
     */
    protected $agents = array();

    /**
     * @var array
     */
    protected $units = array();

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

        $this->setLibraryFromArray('airports', $options['libraries']['apDeps'], 'id', array('apDepIata', 'apDepIcao'));
        $this->setLibraryFromObject('agents', $options['libraries']['agents'], 'id', 'name');
        $this->setLibraryFromObject('units', $options['libraries']['units'], 'id', 'name');

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
            'name' => 'airport',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Airport',
                'empty_option' => '-- Please select --',
                'value_options' => $this->airports,
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Date',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'agent',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Agent',
                'empty_option' => '-- Please select --',
                'value_options' => $this->agents,
            ),
        ));

        $this->add(array(
            'name' => 'quantity',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => array(
                'required' => true,
                'maxlength' => '10',
            ),
            'options' => array(
                'label' => 'Quantity',
            ),
        ));

        $this->add(array(
            'name' => 'unit',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
            ),
            'options' => array(
                'label' => 'Unit',
                'empty_option' => '-- Please select --',
                'value_options' => $this->units,
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
     * @param $libraryName
     * @param ResultSet $data
     * @param string $baseFieldKey
     * @param string $fieldName
     * @return $this
     */
    protected function setLibraryFromObject($libraryName, ResultSet $data, $baseFieldKey = 'id', $fieldName = '')
    {
        if (!$this->{$libraryName}) {
            foreach ($data as $row) {
                if (is_array($fieldName)) {
                    if ($row->{$fieldName[1]} != '') {
                        $fieldValue = $row->{$fieldName[0]} . ' (' . $row->{$fieldName[1]} . ')';
                    } else {
                        $fieldValue = $row->{$fieldName[0]};
                    }
                    $this->{$libraryName}[$row->{$baseFieldKey}] = $fieldValue;
                } else {
                    if ($row->{$fieldName} != '') {
                        $this->{$libraryName}[$row->{$baseFieldKey}] = $row->{$fieldName};
                    }
                }
            }
            uasort($this->{$libraryName}, array($this, 'sortLibrary'));
        }
        return $this;
    }

    /**
     * @param $libraryName
     * @param array $data
     * @param string $baseFieldKey
     * @param string $fieldName
     * @return $this
     */
    protected function setLibraryFromArray($libraryName, array $data, $baseFieldKey = 'id', $fieldName = '')
    {
        if (!$this->{$libraryName}) {
            foreach ($data as $row) {
                if (is_array($fieldName)) {
                    if ($row[$fieldName[1]] != '') {
                        $fieldValue = $row[$fieldName[0]] . ' (' . $row[$fieldName[1]] . ')';
                    } else {
                        $fieldValue = $row[$fieldName[0]];
                    }
                    $this->{$libraryName}[$row[$baseFieldKey]] = $fieldValue;
                } else {
                    if ($row[$fieldName] != '') {
                        $this->{$libraryName}[$row[$baseFieldKey]] = $row[$fieldName];
                    }
                }
            }
            uasort($this->{$libraryName}, array($this, 'sortLibrary'));
        }
        return $this;
    }

    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
