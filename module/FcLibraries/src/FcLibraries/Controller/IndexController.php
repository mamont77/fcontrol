<?php
/**
 * @namespace
 */
namespace FcLibraries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use FcLibrariesSearch\Form\AdvancedSearchForm;
use Zend\View\Model\ViewModel;
use FcLibraries\Model\AircraftModel;
use FcLibraries\Model\AircraftTypeModel;
use FcLibraries\Model\AirOperatorModel;
use FcLibraries\Model\AirportModel;
use FcLibraries\Model\BaseOfPermitModel;
use FcLibraries\Model\CityModel;
use FcLibraries\Model\CountryModel;
use FcLibraries\Model\CurrencyModel;
use FcLibraries\Model\KontragentModel;
use FcLibraries\Model\RegionModel;
use FcLibraries\Model\TypeOfApServiceModel;
use FcLibraries\Model\UnitModel;


/**
 * Class IndexController
 *
 * @package FcLibraries\Controller
 */
class IndexController extends AbstractActionController
{

    /**
     * @var AircraftModel
     */
    protected $aircraftModel;

    /**
     * @var AircraftTypeModel
     */
    protected $aircraftTypeModel;

    /**
     * @var AirOperatorModel
     */
    protected $airOperatorModel;

    /**
     * @var AirportModel
     */
    protected $airportModel;

    /**
     * @var BaseOfPermitModel
     */
    protected $baseOfPermitModel;

    /**
     * @var CityModel
     */
    protected $cityModel;

    /**
     * @var CountryModel
     */
    protected $countryModel;

    /**
     * @var CurrencyModel
     */
    protected $currencyModel;

    /**
     * @var KontragentModel
     */
    protected $kontragentModel;

    /**
     * @var RegionModel
     */
    protected $regionModel;

    /**
     * @var TypeOfApServiceModel
     */
    protected $typeOfApServiceModel;

    /**
     * @var UnitModel
     */
    protected $unitModel;

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        return new ViewModel(array(
                'searchForm' => new AdvancedSearchForm(),
            )
        );
    }

    /**
     * @return AircraftModel
     */
    public function getAircraftModel()
    {
        if (!$this->aircraftModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftModel = $sm->get('FcLibraries\Model\AircraftModel');
        }

        return $this->aircraftModel;
    }

    /**
     * @return AircraftTypeModel
     */
    public function getAircraftTypeModel()
    {
        if (!$this->aircraftTypeModel) {
            $sm = $this->getServiceLocator();
            $this->aircraftTypeModel = $sm->get('FcLibraries\Model\AircraftTypeModel');
        }

        return $this->aircraftTypeModel;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAircraftTypes()
    {
        return $this->getAircraftTypeModel()->fetchAll();
    }

    /**
     * @return AirOperatorModel
     */
    public function getAirOperatorModel()
    {
        if (!$this->airOperatorModel) {
            $sm = $this->getServiceLocator();
            $this->airOperatorModel = $sm->get('FcLibraries\Model\AirOperatorModel');
        }

        return $this->airOperatorModel;
    }

    /**
     * @return AirportModel
     */
    public function getAirportModel()
    {
        if (!$this->airportModel) {
            $sm = $this->getServiceLocator();
            $this->airportModel = $sm->get('FcLibraries\Model\AirportModel');
        }

        return $this->airportModel;
    }

    /**
     * @return BaseOfPermitModel
     */
    public function getBaseOfPermitModel()
    {
        if (!$this->baseOfPermitModel) {
            $sm = $this->getServiceLocator();
            $this->baseOfPermitModel = $sm->get('FcLibraries\Model\BaseOfPermitModel');
        }

        return $this->baseOfPermitModel;
    }

    /**
     * @return CityModel
     */
    public function getCityModel()
    {
        if (!$this->cityModel) {
            $sm = $this->getServiceLocator();
            $this->cityModel = $sm->get('FcLibraries\Model\CityModel');
        }

        return $this->cityModel;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCities()
    {
        return $this->getCityModel()->fetchAll();
    }

    /**
     * @return CountryModel
     */
    public function getCountryModel()
    {
        if (!$this->countryModel) {
            $sm = $this->getServiceLocator();
            $this->countryModel = $sm->get('FcLibraries\Model\CountryModel');
        }

        return $this->countryModel;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCountries()
    {
        return $this->getCountryModel()->fetchAll();
    }

    /**
     * @return CurrencyModel
     */
    public function getCurrencyModel()
    {
        if (!$this->currencyModel) {
            $sm = $this->getServiceLocator();
            $this->currencyModel = $sm->get('FcLibraries\Model\CurrencyModel');
        }

        return $this->currencyModel;
    }

    /**
     * @return KontragentModel
     */
    public function getKontragentModel()
    {
        if (!$this->kontragentModel) {
            $sm = $this->getServiceLocator();
            $this->kontragentModel = $sm->get('FcLibraries\Model\KontragentModel');
        }

        return $this->kontragentModel;
    }

    /**
     * @return RegionModel
     */
    public function getRegionModel()
    {
        if (!$this->regionModel) {
            $sm = $this->getServiceLocator();
            $this->regionModel = $sm->get('FcLibraries\Model\RegionModel');
        }

        return $this->regionModel;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getRegions()
    {
        return $this->getRegionModel()->fetchAll();
    }

    /**
     * @return TypeOfApServiceModel
     */
    public function getTypeOfApServiceModel()
    {
        if (!$this->typeOfApServiceModel) {
            $sm = $this->getServiceLocator();
            $this->typeOfApServiceModel = $sm->get('FcLibraries\Model\TypeOfApServiceModel');
        }

        return $this->typeOfApServiceModel;
    }

    /**
     * @return UnitModel
     */
    public function getUnitModel()
    {
        if (!$this->unitModel) {
            $sm = $this->getServiceLocator();
            $this->unitModel = $sm->get('FcLibraries\Model\UnitModel');
        }

        return $this->unitModel;
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
