<?php
namespace FcLibraries\Form;
use Zend\InputFilter\InputFilter;

class RegionFormInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'text',
            'required' => true,
        ));
    }
}
