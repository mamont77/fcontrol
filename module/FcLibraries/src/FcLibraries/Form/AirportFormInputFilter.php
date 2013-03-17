<?php
namespace FcLibraries\Form;
use Zend\InputFilter\InputFilter;

class AirportFormInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'text',
            'required' => true,
        ));
    }
}
