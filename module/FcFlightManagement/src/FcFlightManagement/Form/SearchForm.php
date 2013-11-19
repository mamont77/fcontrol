<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class SearchForm
 *
 * @package FcFlight\Form
 */
class SearchForm extends Form
{

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'type'       => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Find',
            ),
        ));
    }
}
