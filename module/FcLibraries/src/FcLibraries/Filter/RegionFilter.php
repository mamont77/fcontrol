<?php
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class RegionFilter extends BaseFilter
{

    /**
     * @var string
     */
    protected $table = 'library_region';

    public $id;
    public $name;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            if ($this->id) {
                $noRecordExistsValidators = array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => $this->table,
                        'field' => 'name',
                        'exclude' => array(
                            'field' => 'id',
                            'value' => $this->id
                        ),
                        'adapter' => $this->getDbAdapter(),
                    ),
                );
            } else {
                $noRecordExistsValidators = array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => $this->table,
                        'field' => 'name',
                        'adapter' => $this->getDbAdapter(),
                    ),
                );
            }

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 30,
                        ),
                    ),
                    $noRecordExistsValidators,
                ),
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
