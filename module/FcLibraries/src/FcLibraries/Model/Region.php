<?php
namespace FcLibraries\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use FcLibraries\Model\LibraryModel;

class Region extends LibraryModel
{
    /**
     * @var string
     */
    protected $table = 'library_region';

    public $id;
    public $name;

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
                        'name'    => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => $this->table,
                            'field' => 'name',
//                            'exclude' => array(
//                                'field' => 'id',
//                                'value' => $this->id
//                            ),
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
