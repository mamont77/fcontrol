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
use FcLibraries\Model\TypeOfApServiceModel;
use FcLibraries\Model\UnitModel;
use FcFlight\Model\FlightHeaderModel;
use FcFlight\Model\LegModel;
use FcFlight\Model\PermissionModel;
use FcFlight\Model\RefuelModel;
use FcFlight\Model\ApServiceModel;
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
    protected $_mapFields = array(
        'id',
        'refNumberOrder',
        'dateOrder',
        'kontragent',
        'kontragentShortName',
        'airOperator',
        'airOperatorShortName',
        'aircraftId',
        'aircraftName',
        'aircraftTypeName',
        'alternativeAircraftId1',
        'alternativeAircraftName1',
        'alternativeAircraftTypeName1',
        'alternativeAircraftId2',
        'alternativeAircraftName2',
        'alternativeAircraftTypeName2',
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
     * @var TypeOfApServiceModel
     */
    protected $typeOfApServiceModel;

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
            if (!$row->id) {
                continue;
            }

            foreach ($this->_mapFields as $field) {
                if (isset($row->$field)) {
                    $data[$key][$field] = $row->$field;
                }
            }
            try {
                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasRefuel)) {
                    $data[$key]['refuelStatus'] = 'YES';

                    $refuelIsDone = true;
                    foreach ($hasRefuel as $row) {
                        if ($row['status'] == 0) {
                            $refuelIsDone = false;
                            continue;
                        }
                    }
                    if ($refuelIsDone) {
                        $data[$key]['refuelStatus'] = 'DONE';
                    }
                } else {
                    $data[$key]['refuelStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }

            try {
                $hasPermission = $this->getPermissionModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasPermission)) {
                    $data[$key]['permitStatus'] = 'YES';

                    $permissionIsDone = true;
                    foreach ($hasPermission as $row) {
                        if ($row['status'] == 0) {
                            $permissionIsDone = false;
                            continue;
                        }
                    }
                    if ($permissionIsDone) {
                        $data[$key]['permitStatus'] = 'DONE';
                    }
                } else {
                    $data[$key]['permitStatus'] = 'NO';
                }
            } catch (Exception $e) {
                // do nothing
            }

            try {
                $hasApService = $this->getApServiceModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasApService)) {
                    $data[$key]['apServiceStatus'] = 'YES';

                    $apServiceIsDone = true;
                    foreach ($hasApService as $row) {
                        if ($row['status'] == 0) {
                            $apServiceIsDone = false;
                            continue;
                        }
                    }
                    if ($apServiceIsDone) {
                        $data[$key]['apServiceStatus'] = 'DONE';
                    }
                } else {
                    $data[$key]['apServiceStatus'] = 'NO';
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
            foreach ($this->_mapFields as $field) {
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

                    foreach ($hasPermission as $row) {
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

            try {
                $hasApService = $this->getApServiceModel()->getByHeaderId($data[$key]['id']);
                if (!empty($hasApService)) {
                    $data[$key]['apServiceStatus'] = 'YES';

                    $apServiceIsDone = true;
                    foreach ($hasApService as $row) {
                        if ($row['status'] == 0) {
                            $apServiceIsDone = false;
                            continue;
                        }
                    }
                    if ($apServiceIsDone) {
                        $data[$key]['apServiceStatus'] = 'DONE';
                    }
                } else {
                    $data[$key]['apServiceStatus'] = 'NO';
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
        if ($header->parentId) {
            $header->parentRefNumberOrder = $this->getFlightHeaderModel()->getRefNumberOrderById($header->parentId);
        }

        $legs = $this->getLegModel()->getByHeaderId($header->id);
        $refuels = $this->getRefuelModel()->getByHeaderId($header->id);
        $permissions = $this->getPermissionModel()->getByHeaderId($header->id);
        $apServices = $this->getApServiceModel()->getByHeaderId($header->id);
        $builtAirports = $this->buildAirportsFromLeg($legs);

        $refuelsTotal = 0;
        foreach ($refuels as &$refuel) {
            $refuelsTotal += $refuel['totalPriceUsd'];
            $builtId = $refuel['legId'] . '-' . $refuel['airportId'];
            if (array_key_exists($builtId, $builtAirports)) {
                $refuel['builtAirportName'] = $builtAirports[$builtId];
            }
        }
        $apServicesTotal = 0;
        foreach ($apServices as &$apService) {
            $apServicesTotal += $apService['priceUSD'];
            $builtId = $apService['legId'] . '-' . $apService['airportId'];
            if (array_key_exists($builtId, $builtAirports)) {
                $apService['builtAirportName'] = $builtAirports[$builtId];
            }
        }

        return new ViewModel(array(
            'header' => $header,
            'legs' => $legs,
            'refuels' => $refuels,
            'refuelsTotal' => $refuelsTotal,
            'permissions' => $permissions,
            'apServices' => $apServices,
            'apServicesTotal' => $apServicesTotal,
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
    public function cloneHeaderAction()
    {

        $headerId = (int)$this->params()->fromRoute('id', 0);
        if (!$headerId) {
            return $this->redirect()->toRoute('flight', array(
                'action' => 'add'
            ));
        }
        $header = $this->getFlightHeaderModel()->get($headerId);

        $form = new FlightHeaderForm('flightHeader',
            array(
                'libraries' => array(
                    'kontragent' => $this->getKontragents(),
                    'air_operator' => $this->getAirOperators(),
                    'aircraft' => $this->getAircrafts(),
                )
            )
        );

        $form->bind($header);
        $form->get('submitBtn')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcFlight\Filter\FlightHeaderFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $parentHeader = $this->getFlightHeaderModel()->get($data->id);
                $parentHeader->status = 0;

                $this->setDataForLogger($parentHeader);
                $loggerPlugin = $this->LogPlugin();
                $loggerPlugin->setOldLogRecord($this->dataForLogger);

                $this->getFlightHeaderModel()->save($parentHeader);
                $data = $this->getFlightHeaderModel()->add($data);

                $message = 'Flights ' . $parentHeader->refNumberOrder
                    . ' was successfully cloned as ' . $data['refNumberOrder'];
                $this->flashMessenger()->addSuccessMessage($message);

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

        return array(
            'id' => $headerId,
            'form' => $form,
            'header' => $header,
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
            'parentId' => $data->parentId,
            'Date Order' => $data->dateOrder,
            'Ref Number' => $data->refNumberOrder,
            'Customer' => $data->kontragentShortName,
            'Air Operator' => $data->airOperatorShortName,
            'Aircraft' => $data->aircraftName . ' (' . $data->aircraftTypeName . ')',
            'Alternative Aircraft 1' => $data->alternativeAircraftName1 . ' (' . $data->alternativeAircraftTypeName1 . ')',
            'Alternative Aircraft 2' => $data->alternativeAircraftName2 . ' (' . $data->alternativeAircraftTypeName2 . ')',
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
     * Get TypeOfApService list
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getTypeOfApServices()
    {
        return $this->getTypeOfApServiceModel()->fetchAll();
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
     * Get ApService model
     *
     * @return ApServiceModel
     */
    public function getApServiceModel()
    {
        return $this->getServiceLocator()->get('FcFlight\Model\ApServiceModel');
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
     * @return array
     */
    public function geTypeOfPermissions()
    {
        return array(
            'O/L' => 'O/L',
            'LND' => 'LND',
            'DG' => 'DG',
            'DIP' => 'DIP',
        );
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
    public function redirectForDoneStatus($refNumberOrder)
    {
        $data = $this->getFlightHeaderModel()->getByRefNumberOrder($refNumberOrder);
        if ($data->status == 0) {
            $this->flashMessenger()->addErrorMessage('This flight ' . $refNumberOrder . ' has a status "Done".');

            return $this->redirect()->toRoute('browse',
                array(
                    'action' => 'show',
                    'refNumberOrder' => $refNumberOrder,
                ));
        }
        return $data->status;
    }

    /**
     * @param $legs
     * @return array
     */
    public function buildAirportsFromLeg($legs)
    {
        $airports = array();
        $legsCopy = $legs;
        $legFirst = reset($legs);
        $airports[$legFirst['id'] . '-' . $legFirst['apDepAirportId']] = $legFirst['apDepIcao'] . ' (' . $legFirst['apDepIata'] . '): '
            . $legFirst['dateOfFlight'] . ' ' . $legFirst['apDepTime'];
        foreach ($legs as $leg) {
            $nextLeg = next($legsCopy);
            $selectionValues = $leg['apArrIcao'] . ' (' . $leg['apArrIata'] . '): '
                . $leg['dateOfFlight'] . ' ' . $leg['apArrTime'];

            if (!is_bool($nextLeg)) {
                $selectionValues .= ' ⇒ ' . $nextLeg['dateOfFlight'] . ' ' . $nextLeg['apDepTime']; //✈
            }

            $airports[$leg['id'] . '-' . $leg['apArrAirportId']] = $selectionValues;
        }

        return $airports;
    }
}
