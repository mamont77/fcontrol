<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class RefuelStep1Form
 * @package FcFlightManagement\Form
 */
class RefuelStep1Form extends Form
{
    /**
     * @var string
     */
    protected $_formName = '';

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

        $this->setName($this->_formName);
        $this->setAttribute('method', 'post');

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
}
