<?php
namespace FcLibraries\Form;
use Zend\InputFilter\InputFilter;

/**
 * SearchFormInputFilter
 * @package DluTwBootstrapDemo
 * @copyright David Lukas (c) - http://www.zfdaily.com
 * @license http://www.zfdaily.com/code/license New BSD License
 * @link http://www.zfdaily.com
 * @link https://bitbucket.org/dlu/dlutwbootstrap-demo
 */
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
