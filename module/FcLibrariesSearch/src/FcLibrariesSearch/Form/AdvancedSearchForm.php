<?php
/**
 * @namespace
 */
namespace FcLibrariesSearch\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AdvancedSearchForm extends Form
{
    /**
     * @var array
     */
    protected $aircraft_types = array();

    /**
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setName('advancedSearch');
        $this->setAttributes(array(
                'method' => 'post',
                'action' => '/admin/libraries/advanced_search',
            ));

        //Text
        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Search term...',
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Text',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'library',
            'options' => array(
                'label' => 'Select library',
                'value_options' => array(
                    'library_aircraft' => 'Aircraft',
                    'library_aircraft_type' => 'Type of Aircraft',
                    'library_air_operator' => 'Air Operator',
                    'library_airport' => 'Airport',
                    'library_base_of_permit' => 'Base of Permit',
                    'library_city' => 'City',
                    'library_country' => 'Country',
                    'library_currency' => 'Currency',
                    'library_kontragent' => 'Kontragent',
                    'library_region' => 'Region',
                    'library_type_of_ap_service' => 'Type of AP Service',
                    'library_unit' => 'Unit',
                ),
            ),
            'attributes' => array(
                'value' => 'library_airport',
            )
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search',
            ),
        ));


    }
}
