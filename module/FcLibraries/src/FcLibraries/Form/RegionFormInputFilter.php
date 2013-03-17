<?php
namespace FcLibraries\Form;

use Zend\InputFilter\InputFilter;

class RegionFormInputFilter extends InputFilter
{
    protected $_filters = array(
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
    );
    const TOO_SHORT = 'Long';

    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => $this->_filters,
            //            'validators' => array(
//                array(
//                    'name' => 'StringLength',
//                    'options' => array(
//                        'encoding' => 'UTF-8',
//                        'max' => 30,
//                        'messages' => array(
//                            \Zend\Validator\StringLength::TOO_SHORT => self::TOO_SHORT,
//                        ),
//                    ),
//                ),
//            ),
        ));
    }
}
