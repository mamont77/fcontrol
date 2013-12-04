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
use FcFlight\Form\ApServiceForm;

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
        $result = array();
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
                $result = $this->getRefuelModel()->findByParams($data);

            }
        }

        return array(
            'form' => $searchForm,
            'result' => $result,
        );
    }

    /**
     * @return ViewModel
     */
    public function step2Action()
    {
        $result = array();

        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }

        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $data = $request->getPost();

            if (empty($data['refuelsSelected'])) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/refuel/step1');
            }

            $result = $this->getRefuelModel()->findByParams($data);
        }

        return array(
            'currencies' => $currencies,
            'units' => $units,
            'result' => $result,
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
