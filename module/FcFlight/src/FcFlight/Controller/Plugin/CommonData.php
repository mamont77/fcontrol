<?php
/**
 * CommonData Helper for FcFlight module.
 * @author Ruslan Piskarev
 */

namespace FcFlight\Controller\Plugin;

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
use FcFlight\Model\FlightHeaderModel;
use FcFlight\Model\LegModel;
use FcFlight\Model\RefuelModel;
use FcFlight\Model\SearchModel;

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
     * @var FlightHeaderModel
     */
    protected $flightHeaderModel;

    /**
     * @var LegModel
     */
    protected $legModel;

    /**
     * @var RefuelModel
     */
    protected $refuelModel;

    /**
     * @var SearchModel
     */
    protected $searchModel;

    /**
     * Set Aircraft model
     *
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
     * Get Aircraft model
     *
     * @return AircraftModel
     */
    public function getAircraftModel()
    {
        return $this->aircraftModel;
    }

    /**
     * Get Aircraft list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAircrafts()
    {
        return $this->getAircraftModel()->fetchAll();
    }

    /**
     * Set AircraftType model
     *
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
     * Get AircraftType model
     *
     * @return AircraftTypeModel
     */
    public function getAircraftTypeModel()
    {
        return $this->aircraftTypeModel;
    }

    /**
     * Get AircraftType list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAircraftTypes()
    {
        return $this->getAircraftTypeModel()->fetchAll();
    }

    /**
     * Set AirOperator model
     *
     * @param AirOperatorModel $model
     * @return $this
     */
    public function setAirOperatorModel(AirOperatorModel $model)
    {
        if (!$this->airOperatorModel) {
            $this->airOperatorModel = $model;
        }

        return $this;
    }

    /**
     * Get AirOperator model
     *
     * @return AirOperatorModel
     */
    public function getAirOperatorModel()
    {
        return $this->airOperatorModel;
    }

    /**
     * Get AirOperator list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAirOperators()
    {
        return $this->getAirOperatorModel()->fetchAll();
    }

    /**
     * Set Airport model
     *
     * @param AirportModel $model
     * @return $this
     */
    public function setAirportModel(AirportModel $model)
    {
        if (!$this->airportModel) {
            $this->airportModel = $model;
        }

        return $this;
    }

    /**
     * Get Airport model
     *
     * @return AirportModel
     */
    public function getAirportModel()
    {
        return $this->airportModel;
    }

    /**
     * Get Airport list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAirports()
    {
        return $this->getAirportModel()->fetchAll();
    }

    /**
     * Set BaseOfPermit model
     *
     * @param BaseOfPermitModel $model
     * @return $this
     */
    public function setBaseOfPermitModel(BaseOfPermitModel $model)
    {
        if (!$this->baseOfPermitModel) {
            $this->baseOfPermitModel = $model;
        }

        return $this;
    }

    /**
     * Get BaseOfPermit model
     *
     * @return BaseOfPermitModel
     */
    public function getBaseOfPermitModel()
    {
        return $this->baseOfPermitModel;
    }

    /**
     * Get BaseOfPermit list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getBaseOfPermits()
    {
        return $this->getBaseOfPermitModel()->fetchAll();
    }

    /**
     * Set City model
     *
     * @param CityModel $model
     * @return $this
     */
    public function setCityModel(CityModel $model)
    {
        if (!$this->cityModel) {
            $this->cityModel = $model;
        }

        return $this;
    }

    /**
     * Get City model
     *
     * @return CityModel
     */
    public function getCityModel()
    {
        return $this->cityModel;
    }

    /**
     * Get City list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCities()
    {
        return $this->getCityModel()->fetchAll();
    }

    /**
     * Set Country model
     *
     * @param CountryModel $model
     * @return $this
     */
    public function setCountryModel(CountryModel $model)
    {
        if (!$this->countryModel) {
            $this->countryModel = $model;
        }

        return $this;
    }

    /**
     * Get Country model
     *
     * @return CountryModel
     */
    public function getCountryModel()
    {
        return $this->countryModel;
    }

    /**
     * Get Country list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCountries()
    {
        return $this->getCountryModel()->fetchAll();
    }

    /**
     * Set Currency model
     *
     * @param CurrencyModel $model
     * @return $this
     */
    public function setCurrencyModel(CurrencyModel $model)
    {
        if (!$this->currencyModel) {
            $this->currencyModel = $model;
        }

        return $this;
    }

    /**
     * Get Currency model
     *
     * @return CurrencyModel
     */
    public function getCurrencyModel()
    {
        return $this->currencyModel;
    }

    /**
     * Get Currency list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCurrencies()
    {
        return $this->getCurrencyModel()->fetchAll();
    }

    /**
     * Set Kontragent model
     *
     * @param KontragentModel $model
     * @return $this
     */
    public function setKontragentModel(KontragentModel $model)
    {
        if (!$this->kontragentModel) {
            $this->kontragentModel = $model;
        }

        return $this;
    }

    /**
     * Get Kontragent model
     *
     * @return KontragentModel
     */
    public function getKontragentModel()
    {
        return $this->kontragentModel;
    }

    /**
     * Get Kontragent list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getKontragents()
    {
        return $this->getKontragentModel()->fetchAll();
    }

    /**
     * Set Region model
     *
     * @param RegionModel $model
     * @return $this
     */
    public function setRegionModel(RegionModel $model)
    {
        if (!$this->regionModel) {
            $this->regionModel = $model;
        }

        return $this;
    }

    /**
     * Get Region model
     *
     * @return RegionModel
     */
    public function getRegionModel()
    {
        return $this->regionModel;
    }

    /**
     * Get Region list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getRegions()
    {
        return $this->getRegionModel()->fetchAll();
    }

    /**
     * Set Unit model
     *
     * @param UnitModel $model
     * @return $this
     */
    public function setUnitModel(UnitModel $model)
    {
        if (!$this->unitModel) {
            $this->unitModel = $model;
        }

        return $this;
    }

    /**
     * Get Unit model
     *
     * @return UnitModel
     */
    public function getUnitModel()
    {
        return $this->unitModel;
    }

    /**
     * Get Unit list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getUnits()
    {
        return $this->getUnitModel()->fetchAll();
    }

    /**
     * Set FlightHeader model
     *
     * @param FlightHeaderModel $model
     * @return $this
     */
    public function setFlightHeaderModel(FlightHeaderModel $model)
    {
        if (!$this->flightHeaderModel) {
            $this->flightHeaderModel = $model;
        }

        return $this;
    }

    /**
     * Get FlightHeader model
     *
     * @return FlightHeaderModel
     */
    public function getFlightHeaderModel()
    {
        return $this->flightHeaderModel;
    }

    /**
     * Set Leg model
     *
     * @param LegModel $model
     * @return $this
     */
    public function setLegModel(LegModel $model)
    {
        if (!$this->legModel) {
            $this->legModel = $model;
        }

        return $this;
    }

    /**
     * Get Leg model
     *
     * @return LegModel
     */
    public function getLegModel()
    {
        return $this->legModel;
    }

    /**
     * Get Parent Leg
     *
     * @param $headerId
     * @return array
     */

    public function getParentLeg($headerId)
    {
        return $this->getLegModel()->getByHeaderId($headerId);
    }

    /**
     * Set Refuel model
     *
     * @param RefuelModel $model
     * @return $this
     */
    public function setRefuelModel(RefuelModel $model)
    {
        if (!$this->refuelModel) {
            $this->refuelModel = $model;
        }

        return $this;
    }

    /**
     * Get Refuel model
     *
     * @return RefuelModel
     */
    public function getRefuelModel()
    {
        return $this->refuelModel;
    }

    /**
     * Set Search model
     *
     * @param SearchModel $model
     * @return $this
     */
    public function setSearchModel(SearchModel $model)
    {
        if (!$this->searchModel) {
            $this->searchModel = $model;
        }

        return $this;
    }

    /**
     * Get Search model
     *
     * @return SearchModel
     */
    public function getSearchModel()
    {
        return $this->searchModel;
    }
}