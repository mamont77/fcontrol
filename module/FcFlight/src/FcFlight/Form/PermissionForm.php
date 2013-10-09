<?php

namespace FcFlight\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

class PermissionForm extends BaseForm
{
    /**
     * @var string
     */
    protected $_formName = 'permission';


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
