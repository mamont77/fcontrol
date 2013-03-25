<?php

namespace FcAdmin\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Crypt\Password\Bcrypt;

class User implements InputFilterAwareInterface
{

    public $user_id;
    public $username;
    public $email;
    public $display_name;
    public $password;
    public $role_id;
    public $state;
    protected $_inputFilter;


    public function exchangeArray($data)
    {
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->display_name = (isset($data['display_name'])) ? $data['display_name'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->role_id = (isset($data['role_id'])) ? $data['role_id'] : null;
        $this->state = (isset($data['state'])) ? $data['state'] : null;
    }

    // Add the following method:
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
            $factory = new InputFactory();


            $inputFilter->add($factory->createInput(array(
                'name' => 'user_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 255,
//                            'messages' => array(
//                                \Zend\Validator\StringLength::TOO_LONG => self::INVALID_USERNAME,
//                                \Zend\Validator\StringLength::TOO_SHORT => self::INVALID_USERNAME,
//                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 6,
//                            'messages' => array(
//                                \Zend\Validator\StringLength::TOO_SHORT => self::INVALID_PASSWORD,
//                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'passwordVerify',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 6,
//                            'messages' => array(
//                                \Zend\Validator\StringLength::TOO_SHORT => self::INVALID_PASSWORD,
//                            ),
                        ),

                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
//                            'messages' => array(
//                                \Zend\Validator\Identical::NOT_SAME => self::NOT_SAME,
//                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'role_id',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'state',
                'required' => false,
            )));

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }

    /**
     * @param $newPass
     * @return mixed
     */
    public function changePassword($newPass)
    {
        $crypt = new Bcrypt;
        return $crypt->create($newPass);
    }
}
