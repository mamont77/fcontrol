<?php
namespace FcLibraries\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class RegionFilter implements InputFilterAwareInterface {

    /**
     * @var inputFilter
     */
    protected $inputFilter;

    /**
     * @var Database Adapter
     */
    protected $dbAdapter;

    protected $table = 'library_region';


    /**
     * @var array
     */
    protected $defaultFilters = array(
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
    );

    /**
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    /**
     * @param \Zend\Db\Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     *
     * @return Zend\Db\Adapter
     */
    public function getDbAdapter() {
        return $this->dbAdapter;
    }

    /**
     * @return \Zend\InputFilter\InputFilter
     *
     * Get the input filter (build it first)
     */
    public function getInputFilter() {
        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

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
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => $this->table,
                            'field' => 'name',
                            //'exclude' => array(
                            //    'field' => 'id',
                            //    'value' => $this->id
                            //),
                            'adapter' => $this->getDbAdapter(),
                        ),
                    ),
                ),
            )));
                    $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
