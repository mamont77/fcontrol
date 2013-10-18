<?php
/**
 * @namespace
 */
namespace FcLibrariesSearch\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class AdvancedSearchFilter extends \FcLibraries\Filter\BaseFilter
{
    /**
     * @var string
     */
    protected $table = '';

    public $text;
    public $library;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->text = (isset($data['text'])) ? $data['text'] : null;
        $this->library = (isset($data['library'])) ? $data['library'] : null;
        $this->table = $this->library;
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'text',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 64,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'library',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
