<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\SearchForm;

/**
 * Class SearchController
 *
 * @package FcFlight\Controller
 */
class SearchController extends FlightController
{
    /**
     * @return ViewModel
     */
    public function searchResultAction()
    {

        $result = '';
        $searchForm = new SearchForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            if ($data->dateOrderFrom == ''
                && $data->dateOrderTo == ''
                && $data->customer == ''
                && $data->airOperator == ''
                && $data->flightNumber == ''
                && $data->aircraft == ''
            ) {
                $result = 'Result not found. Enter one or more fields.';
            } else {
                $filter = $this->getServiceLocator()->get('FcFlight\Filter\SearchFilter');
                $searchForm->setInputFilter($filter->getInputFilter());
                $searchForm->setData($request->getPost());
                if ($searchForm->isValid()) {
                    $data = $searchForm->getData();
                    $filter->exchangeArray($data);
                    $result = $this->getSearchModel()->findSearchResult($filter);
                    if (count($result) == 0) {
                        $data = 'Result not found!';
                    } else {
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

                            $builtAirports = array();
                            try {
                                $hasLeg = $this->getLegModel()->getByHeaderId($data[$key]['id']);
                                if (!empty($hasLeg)) {
                                    $data[$key]['legs'] = $hasLeg;
                                    $builtAirports = $this->buildAirportsFromLeg($hasLeg);
                                }
                            } catch (Exception $e) {
                                // do nothing
                            }

                            try {
                                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
                                if (!empty($hasRefuel)) {
                                    $data[$key]['refuelStatus'] = 'YES';
                                    $data[$key]['refuels'] = $hasRefuel;

                                    foreach ($data[$key]['refuels'] as $id => $refuel) {
                                        $builtId = $refuel['legId'] . '-' . $refuel['airportId'];
                                        if (array_key_exists($builtId, $builtAirports)) {
                                            $data[$key]['refuels'][$id]['builtAirportName'] = $builtAirports[$builtId];
                                        }
                                    }
                                    $refuelIsDone = true;
                                    foreach ($data[$key]['refuels'] as $refuel) {
                                        if ($refuel['status'] == 0) {
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
                                    $data[$key]['permissions'] = $hasPermission;

                                    $permissionIsDone = true;
                                    foreach ($data[$key]['permissions'] as $permissions) {
                                        foreach ($permissions['permission'] as $permission) {
                                            if ($permission['status'] == 0) {
                                                $permissionIsDone = false;
                                                continue;
                                            }
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
                                    $data[$key]['apServices'] = $hasApService;
                                    foreach ($data[$key]['apServices'] as $id => $apService) {
                                        $builtId = $apService['legId'] . '-' . $apService['airportId'];
                                        if (array_key_exists($builtId, $builtAirports)) {
                                            $data[$key]['apServices'][$id]['builtAirportName'] = $builtAirports[$builtId];
                                        }
                                    }
                                    $apServiceIsDone = true;
                                    foreach ($data[$key]['apServices'] as $apService) {
                                        if ($apService['status'] == 0) {
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
                    }
                }
            }
        }

        return new ViewModel(array(
            'data' => $data,
            'searchForm' => $searchForm,
            'route' => 'flightsSearch',
        ));
    }
}
