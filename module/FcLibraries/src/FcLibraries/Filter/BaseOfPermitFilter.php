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
 * Class BaseOfPermitFilter
 * @package FcLibraries\Filter
 */
class BaseOfPermitFilter extends BaseFilter
{
    /**
     * @var string
     */
    protected $table = 'library_base_of_permit';

    // Real fields
    public $id;
    public $airportId;
    public $termValidity;
    public $termToTake;
    public $infoToTake;

    //Virtual fields
    public $countryId;
    public $cityId;
    public $airportName;
    public $cityName;
    public $countryName;
    public $countryCode;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        // Real fields
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->airportId = (isset($data['airportId'])) ? $data['airportId'] : null;
        $this->termValidity = (isset($data['termValidity'])) ? $data['termValidity'] : null;
        $this->termToTake = (isset($data['termToTake'])) ? $data['termToTake'] : null;
        $this->infoToTake = (isset($data['infoToTake'])) ? $data['infoToTake'] : null;

        //Virtual fields
        $this->countryId = (isset($data['countryId'])) ? $data['countryId'] : null;
        $this->cityId = (isset($data['cityId'])) ? $data['cityId'] : null;
        $this->airportName = (isset($data['airportName'])) ? $data['airportName'] : null;
        $this->cityName = (isset($data['cityName'])) ? $data['cityName'] : null;
        $this->countryName = (isset($data['countryName'])) ? $data['countryName'] : null;
        $this->countryCode = (isset($data['countryCode'])) ? $data['countryCode'] : null;
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
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airportId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'termValidity',
                'required' => true,
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
                    array(
                        'name' => 'Digits',
                    )
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'termToTake',
                'required' => true,
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
                    array(
                        'name' => 'Digits',
                    )
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'airports',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'infoToTake',
                'required' => true,
                'filters' => $this->defaultFilters,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 400,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
