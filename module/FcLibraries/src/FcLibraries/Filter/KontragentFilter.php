<?php
/**
 * @namespace
 */
namespace FcLibraries\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class KontragentFilter
 * @package FcLibraries\Filter
 */
class KontragentFilter extends BaseFilter
{

    /**
     * @var string
     */
    protected $table = 'library_kontragent';

    public $id;
    public $name;
    public $short_name;
    public $address;
    public $phone1;
    public $phone2;
    public $phone3;
    public $fax;
    public $mail;
    public $sita;
    public $termOfPayment;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->short_name = (isset($data['short_name'])) ? $data['short_name'] : null;
        $this->address = (isset($data['address'])) ? $data['address'] : null;
        $this->phone1 = (isset($data['phone1'])) ? $data['phone1'] : null;
        $this->phone2 = (isset($data['phone2'])) ? $data['phone2'] : null;
        $this->phone3 = (isset($data['phone3'])) ? $data['phone3'] : null;
        $this->fax = (isset($data['fax'])) ? $data['fax'] : null;
        $this->mail = (isset($data['mail'])) ? $data['mail'] : null;
        $this->sita = (isset($data['sita'])) ? $data['sita'] : null;
        $this->termOfPayment = (isset($data['termOfPayment'])) ? $data['termOfPayment'] : 5;
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
                    $this->_noRecordExistsValidators($this->table, 'name', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'short_name',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 15,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'short_name', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'address',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'phone1',
                'required' => false,
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
                    $this->_noRecordExistsValidators($this->table, 'phone1', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'phone2',
                'required' => false,
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
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name' => 'phone3',
                'required' => false,
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
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name' => 'fax',
                'required' => false,
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
                    $this->_noRecordExistsValidators($this->table, 'fax', $this->id),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'mail',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 30,
                        ),
                    ),
                    $this->_noRecordExistsValidators($this->table, 'mail', $this->id),

                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'sita',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'termOfPayment',
                'required' => false,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 2,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
