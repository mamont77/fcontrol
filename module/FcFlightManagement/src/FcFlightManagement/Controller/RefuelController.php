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
use FcFlightManagement\Model\IncomeInvoiceRefuelMainModel;
use FcFlightManagement\Model\IncomeInvoiceRefuelDataModel;

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
     * @var \FcFlightManagement\Model\RefuelModel
     */
    protected $refuelModel;

    /**
     * @var \FcFlightManagement\Model\IncomeInvoiceRefuelMainModel
     */
    protected $incomeInvoiceRefuelMainModel;

    /**
     * @var \FcFlightManagement\Model\IncomeInvoiceRefuelDataModel
     */
    protected $incomeInvoiceRefuelDataModel;


    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep1Action()
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
                return $this->redirect()->toRoute('management/refuel/income-invoice-step1');
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
    public function incomeInvoiceStep2Action()
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
                return $this->redirect()->toRoute('management/refuel/income-invoice-step1');
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
     * @return ViewModel
     */
    public function incomeInvoiceStep3Action()
    {
        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }

        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $result = $request->getPost();

            return array(
                'currencies' => $currencies,
                'units' => $units,
                'result' => $result,
            );
        }

        return $this->redirect()->toRoute('management/refuel/income-invoice-step1');
    }

    /**
     * @return mixed
     */
    public function incomeInvoiceAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
//            \Zend\Debug\Debug::dump($data);
            $invoiceId = $this->getIncomeInvoiceRefuelMainModel()->add($data);

            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $row['preInvoiceRefuelId'] = $row['refuelId'];
                $this->getIncomeInvoiceRefuelDataModel()->add($row);
            }

            $message = "Refuel income invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/refuel/income-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/refuel/income-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/refuel/income-invoice-step1');
        }

        $header = $this->getIncomeInvoiceRefuelMainModel()->get($invoiceId);
        $data = $this->getIncomeInvoiceRefuelDataModel()->getByInvoiceId($invoiceId);

        foreach ($data as $key => $row) {
            $header->data[$row->refuelId] = $row;
        }

        return new ViewModel(array(
            'header' => $header,
        ));

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

    public function getIncomeInvoiceRefuelMainModel()
    {
        if (!$this->incomeInvoiceRefuelMainModel) {
            $sm = $this->getServiceLocator();
            $this->incomeInvoiceRefuelMainModel = $sm->get('FcFlightManagement\Model\IncomeInvoiceRefuelMainModel');
        }

        return $this->incomeInvoiceRefuelMainModel;
    }

    public function getIncomeInvoiceRefuelDataModel()
    {
        if (!$this->incomeInvoiceRefuelDataModel) {
            $sm = $this->getServiceLocator();
            $this->incomeInvoiceRefuelDataModel = $sm->get('FcFlightManagement\Model\IncomeInvoiceRefuelDataModel');
        }

        return $this->incomeInvoiceRefuelDataModel;
    }
}
