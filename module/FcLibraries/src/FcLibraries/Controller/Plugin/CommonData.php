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
     * @var
     */
    protected $aircraftTypeModel;

    /**
     * @param AircraftTypeModel $model
     * @return $this
     */

    public function setAircraftTypeModel(\FcLibraries\Model\AircraftTypeModel $model)
    {
        $this->aircraftTypeModel = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getAircraftTypeModel()
    {
        return $this->aircraftTypeModel;
    }

    /**
     * @return mixed
     */
    public function getAircraftTypes()
    {
        return $this->getAircraftTypeModel()->fetchAll();
    }
}