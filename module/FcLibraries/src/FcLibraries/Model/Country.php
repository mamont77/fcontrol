<?php
namespace FcLibraries\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class Country implements ServiceLocatorAwareInterface, InputFilterAwareInterface
{
    public $id;
    public $name;
    public $region;
    public $region_name;
    public $code;
    protected $_inputFilter;

    /**
     * @var array
     */
    protected $_filters = array(
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
    );

    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->region = (isset($data['region'])) ? $data['region'] : null;
        $this->region_name = (isset($data['region_name'])) ? $data['region_name'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @return void|\Zend\InputFilter\InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => $this->_filters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 30,
                        ),
                    ),
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'library_country',
                            'field' => 'name',
                            'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'region',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'code',
                'required' => true,
                'filters' => $this->_filters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 3,
                        ),
                    ),
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'library_country',
                            'field' => 'code',
                            'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                        ),
                    ),
                ),
            )));

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
