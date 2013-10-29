<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
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
use FcFlight\Model\PermissionModel;
use FcFlight\Model\RefuelModel;
use FcFlight\Model\HotelModel;
use FcFlight\Model\TransferModel;
use FcFlight\Model\ApServiceModel;
use FcFlight\Model\HandingModel;
use FcFlight\Model\TypeOfPermissionModel;
use FcFlight\Model\SearchModel;
use FcFlight\Form\FlightHeaderForm;
use FcFlight\Form\SearchForm;

/**
 * Class FlightController
 * @package FcFlight\Controller
 */
class FlightController extends AbstractActionController
{
    /**
     * @var array
     */
    protected $mapFields = array(
        'id',
        'refNumberOrder',
        'dateOrder',
        'kontragent',
        'kontragentShortName',
        'airOperator',
        'airOperatorShortName',
        'aircraft',
        'aircraftType',
        'aircraftTypeName',
        'status',
        'refuelStatus',
        'permitStatus',
    );

    /**
     * @var array
     */
    protected $dataForLogger = array();

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
     * @var PermissionModel
     */
    protected $permissionModel;

    /**
     * @var RefuelModel
     */
    protected $refuelModel;

    /**
     * @var HotelModel
     */
    protected $hotelModel;

    /**
     * @var TypeOfPermissionModel
     */
//    protected $typeOfPermissionModel;

    /**
     * @var SearchModel
     */
    protected $searchModel;

    /**
     * @return ViewModel
     */
    public function activeAction()
    {
        $select = new Select();

        $orderByMaster = 'dateOrder';
        $orderAsType = Select::ORDER_DESCENDING;

        $orderBy = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : $orderByMaster;
        $orderAs = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : $orderAsType;

        if ($orderBy == $orderByMaster && $orderAsType == $orderAs) {
            $result = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
                . ', id ' . $orderAs));
        } else {
            $result = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs));
        }
        $result->current();

        $data = array();
        foreach ($result as $key => $row) {
            foreach ($this->mapFields as $field) {
                if (isset($row->$field)) {
                    $data[$key][$field] = $row->$field;
                }
            }
            try {
                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasRefuel)) {
                    $data[$key]['refuelStatus'] = 'YES';
                } else {
                    $data[$key]['refuelStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }

            try {
                $hasPermission = $this->getPermissionModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasPermission)) {
                    $data[$key]['permitStatus'] = 'CNFMD';

                    foreach ($hasPermission as $row){
                        if ($row['check'] != 'RECEIVED') {
                            $data[$key]['permitStatus'] = 'YES';
                            continue;
                        }
                    }
                } else {
                    $data[$key]['permitStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }
        }

        return new ViewModel(array(
            'order_by' => $orderBy,
            'order' => $orderAs,
            'data' => $data,
            'searchForm' => new SearchForm(),
            'route' => 'flightsActive',
        ));
    }

    /**
     * @return ViewModel
     */
    public function archivedAction()
    {
        $select = new Select();

        $orderByMaster = 'dateOrder';
        $orderAsType = Select::ORDER_DESCENDING;

        $orderBy = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : $orderByMaster;
        $orderAs = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : $orderAsType;

        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        if ($orderBy == $orderByMaster && $orderAsType == $orderAs) {
            $result = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs
                . ', id ' . $orderAs), 0);
        } else {
            $result = $this->getFlightHeaderModel()->fetchAll($select->order($orderBy . ' ' . $orderAs), 0);
        }
        $itemsPerPage = 20;
        $result->current();

        $data = array();
        foreach ($result as $key => $row) {
            foreach ($this->mapFields as $field) {
                if (isset($row->$field)) {
                    $data[$key][$field] = $row->$field;
                }
            }
            try {
                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasRefuel)) {
                    $data[$key]['refuelStatus'] = 'YES';
                } else {
                    $data[$key]['refuelStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }

            try {
                $hasPermission = $this->getPermissionModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasPermission)) {
                    $data[$key]['permitStatus'] = 'CNFMD';

                    foreach ($hasPermission as $row){
                        if ($row['check'] != 'RECEIVED') {
                            $data[$key]['permitStatus'] = 'YES';
                            continue;
                        }
                    }
                } else {
                    $data[$key]['permitStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }
        }

        $pagination = new Paginator(new paginatorIterator($result));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $orderBy,
            'order' => $orderAs,
            'data' => $data,
            'page' => $page,
            'pagination' => $pagination,
            'searchForm' => new SearchForm(),
            'route' => 'flightsArchived',
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function showAction()
    {
        $refNumberOrder = (string)$this->params()->fromRoute('refNumberOrder', '');
        $refNumberOrder = urldecode($refNumberOrder);

        if (empty($refNumberOrder)) {
            return $this->redirect()->toRoute('home', array(
                'action' => 'active'
            ));
        }

        $header = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);

        $legs = $this->getLegModel()->getByHeaderId($header->id);
        $refuels = $this->getRefuelModel()->getByHeaderId($header->id);
        $permissions = $this->getPermissionModel()->getByHeaderId($header->id);
        $hotels = $this->getHotelModel()->getByHeaderId($header->id);
        $transfers = $this->getTransferModel()->getByHeaderId($header->id);
        $apServices = $this->getApServiceModel()->getByHeaderId($header->id);
        $handing = $this->getHandingModel()->getByHeaderId($header->id);
        $typeOfPermissions = $this->getTypeOfPermissionModel()->getByHeaderId($header->id);

        return new ViewModel(array(
            'header' => $header,
            'legs' => $legs,
            'refuels' => $refuels,
            'permissions' => $permissions,
            'hotels' => $hotels,
            'transfers' => $transfers,
            'apServices' => $apServices,
            'handing' => $handing,
            'typeOfPermissions' => $typeOfPermissions,
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addHeaderAction()
    {

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->getKontragents(),
                    'air_operator' => $this->getAirOperators(),
                    'aircraft' => $this->getAircrafts(),
                )
            )
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightHeaderFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $data = $this->getFlightHeaderModel()->add($filter);

                $message = "Flights '" . $data['refNumberOrder'] . "' was successfully added.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getFlightHeaderModel()->get($data['lastInsertValue']));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Info($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('browse',
                    array(
                        'action' => 'show',
                        'refNumberOrder' => $data['refNumberOrder'],
                    ));
            }
        }
        return array('form' => $form);
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function editHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'add'
            ));
        }
        $data = $this->getFlightHeaderModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->getKontragents(),
                    'air_operator' => $this->getAirOperators(),
                    'aircraft' => $this->getAircrafts(),
                )
            )
        );

        $form->bind($data);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightHeaderFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $refNumberOrder = $this->getFlightHeaderModel()->save($data);

                $message = "Flights '" . $refNumberOrder . "' was successfully saved.";
                $this->flashMessenger()->addSuccessMessage($message);

                $this->setDataForLogger($this->getFlightHeaderModel()->get($id));
                $loggerPlugin->setNewLogRecord($this->dataForLogger);
                $loggerPlugin->setLogMessage($message);

                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Notice($loggerPlugin->getLogMessage());

                return $this->redirect()->toRoute('home');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');

                $loggerPlugin = $this->LogPlugin();
                $this->setDataForLogger($this->getFlightHeaderModel()->get($id));
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $refNumberOrder = (string)$request->getPost('refNumberOrder');
                $this->getFlightHeaderModel()->remove($id);

                $message = "Flights '" . $refNumberOrder . "' was successfully deleted.";
                $this->flashMessenger()->addSuccessMessage($message);

                $loggerPlugin->setLogMessage($message);
                $logger = $this->getServiceLocator()->get('logger');
                $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
                $logger->Warn($loggerPlugin->getLogMessage());
            }

            // Redirect to list
            return $this->redirect()->toRoute('home');
        }

        return array(
            'id' => $id,
            'data' => $this->getFlightHeaderModel()->get($id)
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function statusHeaderAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        $data = $this->getFlightHeaderModel()->get($id);

        $this->setDataForLogger($data);
        $loggerPlugin = $this->LogPlugin();
        $loggerPlugin->setOldLogRecord($this->dataForLogger);

        $data->status = ($data->status) ? 0 : 1;

        $this->getFlightHeaderModel()->save($data);

        $message = "Status for flights '" . $data->refNumberOrder . "' was successfully switched.";
        $this->flashMessenger()->addSuccessMessage($message);

        $this->setDataForLogger($data);
        $loggerPlugin->setNewLogRecord($this->dataForLogger);
        $loggerPlugin->setLogMessage($message);

        $logger = $this->getServiceLocator()->get('logger');
        $logger->addExtra(array('username' => $loggerPlugin->getCurrentUserName(), 'component' => 'flight'));
        $logger->Notice($loggerPlugin->getLogMessage());

        return $this->redirect()->toRoute('browse',
            array(
                'action' => 'show',
                'refNumberOrder' => $data->refNumberOrder,
            ));
    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Date Order' => $data->dateOrder,
            'Ref Number' => $data->refNumberOrder,
            'Customer' => $data->kontragentShortName,
            'Air Operator' => $data->airOperatorShortName,
            'Aircraft' => $data->aircraft . ' (' . $data->aircraftTypeName . ')',
            'Status' => ($data->status == 1) ? 'In process' : 'Done'
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
     * Get Aircraft list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAircrafts()
    {
        return $this->getAircraftModel()->fetchAll();
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
     * Get AircraftType list
     *
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
     * Get AirOperator list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAirOperators()
    {
        return $this->getAirOperatorModel()->fetchAll();
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
     * Get Airport list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getAirports()
    {
        return $this->getAirportModel()->fetchAll();
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
     * Get BaseOfPermit list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getBaseOfPermits()
    {
        return $this->getBaseOfPermitModel()->fetchAll();
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
     * Get City list
     *
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
     * Get Country list
     *
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
     * Get Currency list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCurrencies()
    {
        return $this->getCurrencyModel()->fetchAll();
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
     * Get Kontragent list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getKontragents()
    {
        return $this->getKontragentModel()->fetchAll();
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
     * Get Region list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getRegions()
    {
        return $this->getRegionModel()->fetchAll();
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
     * Get Unit list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getUnits()
    {
        return $this->getUnitModel()->fetchAll();
    }

    /**
     * Get FlightHeader model
     *
     * @return FlightHeaderModel
     */
    public function getFlightHeaderModel()
    {
        if (!$this->flightHeaderModel) {
            $sm = $this->getServiceLocator();
            $this->flightHeaderModel = $sm->get('FcFlight\Model\FlightHeaderModel');
        }

        return $this->flightHeaderModel;
    }

    /**
     * Get Leg model
     *
     * @return LegModel
     */
    public function getLegModel()
    {
        if (!$this->legModel) {
            $sm = $this->getServiceLocator();
            $this->legModel = $sm->get('FcFlight\Model\LegModel');
        }

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
     * Get Permission model
     *
     * @return PermissionModel
     */
    public function getPermissionModel()
    {
        if (!$this->permissionModel) {
            $sm = $this->getServiceLocator();
            $this->permissionModel = $sm->get('FcFlight\Model\PermissionModel');
        }

        return $this->permissionModel;
    }

    /**
     * Get Refuel model
     *
     * @return RefuelModel
     */
    public function getRefuelModel()
    {
        if (!$this->refuelModel) {
            $sm = $this->getServiceLocator();
            $this->refuelModel = $sm->get('FcFlight\Model\RefuelModel');
        }

        return $this->refuelModel;
    }

    /**
     * Get Hotel model
     *
     * @return HotelModel
     */
    public function getHotelModel()
    {
        if (!$this->hotelModel) {
            $sm = $this->getServiceLocator();
            $this->hotelModel = $sm->get('FcFlight\Model\HotelModel');
        }

        return $this->hotelModel;
    }

    /**
     * Get Transfer model
     *
     * @return TransferModel
     */
    public function getTransferModel()
    {
        return $this->getServiceLocator()->get('FcFlight\Model\TransferModel');
    }

    /**
     * Get ApService model
     *
     * @return ApServiceModel
     */
    public function getApServiceModel()
    {
        return $this->getServiceLocator()->get('FcFlight\Model\ApServiceModel');
    }

    /**
     * Get Handing model
     *
     * @return HandingModel
     */
    public function getHandingModel()
    {
        return $this->getServiceLocator()->get('FcFlight\Model\HandingModel');
    }

    /**
     * Get TypeOfPermission model
     *
     * @return TypeOfPermissionModel
     */
    public function getTypeOfPermissionModel()
    {
        return $this->getServiceLocator()->get('FcFlight\Model\TypeOfPermissionModel');
    }

    /**
     * Get Search model
     *
     * @return SearchModel
     */
    public function getSearchModel()
    {
        if (!$this->searchModel) {
            $sm = $this->getServiceLocator();
            $this->searchModel = $sm->get('FcFlight\Model\SearchModel');
        }

        return $this->searchModel;
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

    /**
     * @param $refNumberOrder
     * @return string
     */
    public function redirectForDoneStatus($refNumberOrder) {
        $data = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        if($data->status == 0) {
            $this->flashMessenger()->addErrorMessage('This flight ' . $refNumberOrder . ' has a status "Done".');

            return $this->redirect()->toRoute('browse',
                array(
                    'action' => 'show',
                    'refNumberOrder' => $refNumberOrder,
                ));
        }
        return $data->status;
    }
}
