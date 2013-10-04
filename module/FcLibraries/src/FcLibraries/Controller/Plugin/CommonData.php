<?php
/**
 * CommonData Helper for FcLibraries module.
 * @author Ruslan Piskarev
 */

namespace FcLibraries\Controller\Plugin;

use Zend\Di\ServiceLocator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\AbstractActionController;
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
use FcLibraries\Model\UnitModel;

class CommonData extends AbstractPlugin
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
     * @var UnitModel
     */
    protected $unitModel;

    /**
     * @param AircraftModel $model
     * @return $this
     */
    public function setAircraftModel(AircraftModel $model)
    {
        if (!$this->aircraftModel) {
            $this->aircraftModel = $model;
        }
        return $this;
    }

    /**
     * @return AircraftModel
     */
    public function getAircraftModel()
    {
        return $this->aircraftModel;
    }

    /**
     * @param AircraftTypeModel $model
     * @return $this
     */
    public function setAircraftTypeModel(AircraftTypeModel $model)
    {
        if (!$this->aircraftTypeModel) {
            $this->aircraftTypeModel = $model;
        }
        return $this;
    }

    /**
     * @return AircraftTypeModel
     */
    public function getAircraftTypeModel()
    {
        return $this->aircraftTypeModel;
    }

    /**
     * @param AirOperatorModel $model
     * @return $this
     */
    public function setAirOperatorModel(AirOperatorModel $model)
    {
        $this->airOperatorModel = $model;
        return $this;
    }

    /**
     * @return AirOperatorModel
     */
    public function getAirOperatorModel()
    {
        return $this->airOperatorModel;
    }

    /**
     * @param AirportModel $model
     * @return $this
     */
    public function setAirportModel(AirportModel $model)
    {
        $this->airportModel = $model;
        return $this;
    }

    /**
     * @return AirportModel
     */
    public function getAirportModel()
    {
        return $this->airportModel;
    }

    /**
     * @param BaseOfPermitModel $model
     * @return $this
     */
    public function setBaseOfPermitModel(BaseOfPermitModel $model)
    {
        $this->baseOfPermitModel = $model;
        return $this;
    }

    /**
     * @return BaseOfPermitModel
     */
    public function getBaseOfPermitModel()
    {
        return $this->baseOfPermitModel;
    }

    /**
     * @param CityModel $model
     * @return $this
     */
    public function setCityModel(CityModel $model)
    {
        $this->cityModel = $model;
        return $this;
    }

    /**
     * @return CityModel
     */
    public function getCityModel()
    {
        return $this->cityModel;
    }

    /**
     * @param CountryModel $model
     * @return $this
     */
    public function setCountryModel(CountryModel $model)
    {
        $this->countryModel = $model;
        return $this;
    }

    /**
     * @return CountryModel
     */
    public function getCountryModel()
    {
        return $this->countryModel;
    }

    /**
     * @param CurrencyModel $model
     * @return $this
     */
    public function setCurrencyModel(CurrencyModel $model)
    {
        $this->currencyModel = $model;
        return $this;
    }

    /**
     * @return CurrencyModel
     */
    public function getCurrencyModel()
    {
        return $this->currencyModel;
    }

    /**
     * @param KontragentModel $model
     * @return $this
     */
    public function setKontragentModel(KontragentModel $model)
    {
        $this->kontragentModel = $model;
        return $this;
    }

    /**
     * @return KontragentModel
     */
    public function getKontragentModel()
    {
        return $this->kontragentModel;
    }

    /**
     * @param RegionModel $model
     * @return $this
     */
    public function setRegionModel(RegionModel $model)
    {
        $this->regionModel = $model;
        return $this;
    }

    /**
     * @return RegionModel
     */
    public function getRegionModel()
    {
        return $this->regionModel;
    }

    /**
     * @param UnitModel $model
     * @return $this
     */
    public function setUnitModel(UnitModel $model)
    {
        $this->unitModel = $model;
        return $this;
    }

    /**
     * @return UnitModel
     */
    public function getUnitModel()
    {
        return $this->unitModel;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAircraftTypes()
    {
        return $this->getAircraftTypeModel()->fetchAll();
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCities()
    {
        return $this->getCityModel()->fetchAll();
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCountries()
    {
        return $this->getCountryModel()->fetchAll();
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getRegions()
    {
        return $this->getRegionModel()->fetchAll();
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