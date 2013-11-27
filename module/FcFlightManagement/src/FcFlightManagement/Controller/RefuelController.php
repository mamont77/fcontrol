<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Controller;

use FcFlightManagement\Form\RefuelStep1Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Controller\FlightController;
use FcFlight\Model\FlightHeaderModel;
use FcFlight\Model\LegModel;
use FcFlight\Model\PermissionModel;
use FcFlightManagement\Model\RefuelModel;
use FcFlight\Model\ApServiceModel;

/**
 * Class RefuelController
 * @package FcFlightManagement\Controller
 */
class RefuelController extends FlightController
{
    /**
     * @var array
     */
    protected $_mapFields = array(
        'dateOrderFrom',
        'dateOrderTo',
        'aircraftId',
        'agentId',
        'airportId',
        'customerId',
        'airOperatorId',
    );

    /**
     * @return ViewModel
     */
    public function step1Action()
    {
        $searchForm = new RefuelStep1Form('managementRefuelStep1',
            array(
                'libraries' => array(
                    'aircrafts' => $this->getAircrafts(),
                    'agents' => $this->getKontragents(),
                    'airports' => $this->getAirports(),
                    'customers' => $this->getKontragents(),
                    'airOperators' => $this->getAirOperators(),
                )
            )
        );

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();

            $postIsEmpty = true;
            foreach ($this->_mapFields as $field) {
                if (isset($data->$field) && !empty($data->$field)) {
                    $postIsEmpty = false;
                    continue;
                }
            }

            if ($postIsEmpty) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/refuel/step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\RefuelStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
//                $filter->exchangeArray($data);
                $result = $this->getRefuelModel()->findByParams($data);

//                    if (count($result) == 0) {
//                        $data = 'Result not found!';
//                    } else {
//                        $data = array();
//                        foreach ($result as $key => $row) {
//                            foreach ($this->_mapFields as $field) {
//                                if (isset($row->$field)) {
//                                    $data[$key][$field] = $row->$field;
//                                }
//                            }
//                            try {
//                                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
//                                if (!empty($hasRefuel)) {
//                                    $data[$key]['refuelStatus'] = 'YES';
//                                } else {
//                                    $data[$key]['refuelStatus'] = 'NO';
//                                }
//                            } catch (Exception $e) {
//                                // do nothing
//                            }
//
//                            $data[$key]['permitStatus'] = 'NO';
//                        }
            }
        }

        return array(
            'form' => $searchForm
        );
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
            $this->refuelModel = $sm->get('FcFlightManagement\Model\RefuelModel');
        }

        return $this->refuelModel;
    }
}
