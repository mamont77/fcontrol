<?php
/**
 * @namespace
 */
namespace FcAdmin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class Role
 * @package FcAdmin\Model
 */
class Role implements InputFilterAwareInterface
{

    public $role_id;

    public function exchangeArray($data)
    {
        $this->role_id = (isset($data['role_id'])) ? $data['role_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
     {
         if (!$this->_inputFilter) {
             $inputFilter = new InputFilter();
             $this->_inputFilter = $inputFilter;
         }

         return $this->_inputFilter;
     }


}
