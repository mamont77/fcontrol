<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\ApServiceForm;
use FcFlight\Controller\FlightController;
use FcFlightManagement\Form\PermissionIncomeInvoiceStep1Form;
use FcFlightManagement\Form\PermissionOutcomeInvoiceStep1Form;
use FcFlightManagement\Model\PermissionIncomeInvoiceSearchModel;
use FcFlightManagement\Model\PermissionOutcomeInvoiceSearchModel;
use DOMPDFModule\View\Model\PdfModel;

/**
 * Class PermissionController
 * @package FcFlightManagement\Controller
 */
class PermissionController extends FlightController
{
    /**
     * Fields for search
     *
     * @var array
     */
    protected $_searchMapFields = array(
        'dateFrom',
        'dateTo',
        'aircraftId',
        'agentId',
        'countryId',
        'airportDepId',
        'airportArrId',
        'customerId',
        'airOperatorId',
        'typeOfInvoice',
    );

    /**
     * @var \FcFlightManagement\Model\PermissionIncomeInvoiceSearchModel
     */
    protected $permissionIncomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\PermissionIncomeInvoiceMainModel
     */
    protected $permissionIncomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\PermissionIncomeInvoiceDataModel
     */
    protected $permissionIncomeInvoiceDataModel;

    /**
     * @var \FcFlightManagement\Model\PermissionOutcomeInvoiceSearchModel
     */
    protected $permissionOutcomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\PermissionOutcomeInvoiceMainModel
     */
    protected $permissionOutcomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\PermissionOutcomeInvoiceDataModel
     */
    protected $permissionOutcomeInvoiceDataModel;


    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep1Action()
    {
        $result = array();
        $advancedDataWithIncomeInvoice = array();
        $advancedDataWithOutIncomeInvoice = array();

        $searchForm = new PermissionIncomeInvoiceStep1Form('permissionIncomeInvoiceStep1',
            array(
                'libraries' => array(
                    'aircrafts' => $this->getAircrafts(),
                    'agents' => $this->getKontragents(),
                    'countries' => $this->getCountries(),
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
            foreach ($this->_searchMapFields as $field) {
                if (isset($data->$field) && !empty($data->$field)) {
                    $postIsEmpty = false;
                    continue;
                }
            }

            if ($postIsEmpty) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/permission/income-invoice-step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\PermissionIncomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getPermissionIncomeInvoiceSearchModel()->findByParams($data);
            }
        }

        return array(
            'form' => $searchForm,
            'result' => $result,
            'advancedDataWithIncomeInvoice' => $advancedDataWithIncomeInvoice,
            'advancedDataWithOutIncomeInvoice' => $advancedDataWithOutIncomeInvoice,
        );
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep2Action()
    {
        $request = $this->getRequest();

        $result = array();
        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();
        $aircrafts = array();
        $aircraftsObj = $this->getAircrafts();
        foreach ($aircraftsObj as $aircraft) {
            $aircrafts[$aircraft->id] = $aircraft->aircraft_type_name . ' (' . $aircraft->reg_number . ')';
        }
        $typesOfPermission = $this->getTypeOfPermissions();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (empty($data['rowsSelected'])) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/permission/income-invoice-step1');
            }

            $result = $this->getPermissionIncomeInvoiceSearchModel()->findByParams($data);
        }

        return array(
            'currencies' => $currencies,
            'units' => $units,
            'aircrafts' => $aircrafts,
            'typesOfPermission' => $typesOfPermission,
            'result' => $result,
        );
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep3Action()
    {
        $request = $this->getRequest();

        $result = array();
        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();
        $aircrafts = array();
        $aircraftsObj = $this->getAircrafts();
        foreach ($aircraftsObj as $aircraft) {
            $aircrafts[$aircraft->id] = $aircraft->aircraft_type_name . ' (' . $aircraft->reg_number . ')';
        }
        $typesOfPermission = $this->getTypeOfPermissions();

        if ($request->isPost()) {
            $result = $request->getPost();

            return array(
                'currencies' => $currencies,
                'units' => $units,
                'aircrafts' => $aircrafts,
                'typesOfPermission' => $typesOfPermission,
                'result' => $result,
            );
        }

        return $this->redirect()->toRoute('management/permission/income-invoice-step1');
    }

    /**
     * @return mixed
     */
    public function incomeInvoiceAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $invoiceId = $this->getPermissionIncomeInvoiceMainModel()->add($data);

            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getPermissionIncomeInvoiceDataModel()->add($row);
            }

            $message = "AP Service income invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/permission/income-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/permission/income-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/permission/income-invoice-step1');
        }

        $header = $this->getPermissionIncomeInvoiceMainModel()->get($invoiceId);
        $data = $this->getPermissionIncomeInvoiceDataModel()->getByInvoiceId($invoiceId);

        foreach ($data as $row) {
            $header->data[$row->incomeInvoiceDataId] = $row;
        }

        return new ViewModel(array(
            'header' => $header,
        ));

    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceStep1Action()
    {
        $result = array();
        $incomeInvoiceData = array();
        $outcomeInvoiceData = array();

        $searchForm = new PermissionOutcomeInvoiceStep1Form('permissionOutcomeInvoiceStep1',
            array(
                'libraries' => array(
                    'aircrafts' => $this->getAircrafts(),
                    'agents' => $this->getKontragents(),
                    'countries' => $this->getCountries(),
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
            foreach ($this->_searchMapFields as $field) {
                if (isset($data->$field) && !empty($data->$field)) {
                    $postIsEmpty = false;
                    continue;
                }
            }

            if ($postIsEmpty) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\PermissionOutcomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getPermissionOutcomeInvoiceSearchModel()->findByParams($data);
            }
        }

        return array(
            'form' => $searchForm,
            'result' => $result,
            'incomeInvoiceData' => $incomeInvoiceData,
            'outcomeInvoiceData' => $outcomeInvoiceData,
        );
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceStep2Action()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
        }

        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();
        $aircrafts = array();
        $aircraftsObj = $this->getAircrafts();
        foreach ($aircraftsObj as $aircraft) {
            $aircrafts[$aircraft->id] = $aircraft->aircraft_type_name . ' (' . $aircraft->reg_number . ')';
        }
        $typesOfPermission = $this->getTypeOfPermissions();

        $data = $request->getPost();

        if (empty($data['rowsSelected'])) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
        }

        $result = $this->getPermissionOutcomeInvoiceSearchModel()->findByParams($data);

        $customerId = null;
        foreach ($result as $row) {
            $customerId = $row->incomeInvoiceMainAgentId;
            break;
        }
        $newInvoiceNumber = $this->getPermissionOutcomeInvoiceMainModel()->generateNewInvoiceNumber($customerId);

        return array(
            'newInvoiceNumber' => $newInvoiceNumber,
            'currencies' => $currencies,
            'typesOfPermission' => $typesOfPermission,
            'aircrafts' => $aircrafts,
            'units' => $units,
            'result' => $result,
        );
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceStep3Action()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
        }

        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();
        $aircrafts = array();
        $aircraftsObj = $this->getAircrafts();
        foreach ($aircraftsObj as $aircraft) {
            $aircrafts[$aircraft->id] = $aircraft->aircraft_type_name . ' (' . $aircraft->reg_number . ')';
        }
        $typesOfPermission = $this->getTypeOfPermissions();

        $result = $request->getPost();

        return array(
            'currencies' => $currencies,
            'typesOfPermission' => $typesOfPermission,
            'aircrafts' => $aircrafts,
            'units' => $units,
            'result' => $result,
        );

    }

    /**
     * @return mixed
     */
    public function outcomeInvoiceAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();

            $invoiceId = $this->getPermissionOutcomeInvoiceMainModel()->add($data);
            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getPermissionOutcomeInvoiceDataModel()->add($row, false);
            }
            foreach ($data['subData'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getPermissionOutcomeInvoiceDataModel()->add($row, true);
            }

            $message = "AP Service outcome invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/permission/outcome-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
        }

        $header = $this->getPermissionOutcomeInvoiceMainModel()->get($invoiceId);
        $data = $this->getPermissionOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId, false);
        foreach ($data as $row) {
            $header->data[$row->outcomeInvoiceDataId] = $row;
        }
        $subData = $this->getPermissionOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId, true);
        foreach ($subData as $row) {
            $header->subData[$row->outcomeInvoiceDataId] = $row;
        }

        return new ViewModel(array(
            'header' => $header,
        ));
    }

    public function outcomeInvoicePrintAction()
    {
        $pdf = new PdfModel();
//        $pdf = new ViewModel();
        $pdf->setOption('filename', 'monthly-report2'); // Triggers PDF download, automatically appends ".pdf"
        $pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
        $pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"

        // To set view variables
        $pdf->setVariables(array(
            'message' => 'Hello <b>Word</b>!!!'
        ));

        return $pdf;
    }

    /**
     * Get PermissionIncomeInvoiceSearchModel
     *
     * @return array|PermissionIncomeInvoiceSearchModel|object
     */
    public function getPermissionIncomeInvoiceSearchModel()
    {
        if (!$this->permissionIncomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->permissionIncomeInvoiceSearchModel
                = $sm->get('FcFlightManagement\Model\PermissionIncomeInvoiceSearchModel');
        }

        return $this->permissionIncomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\PermissionIncomeInvoiceMainModel|object
     */
    public function getPermissionIncomeInvoiceMainModel()
    {
        if (!$this->permissionIncomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->permissionIncomeInvoiceMainModel
                = $sm->get('FcFlightManagement\Model\PermissionIncomeInvoiceMainModel');
        }

        return $this->permissionIncomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\PermissionIncomeInvoiceDataModel|object
     */
    public function getPermissionIncomeInvoiceDataModel()
    {
        if (!$this->permissionIncomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->permissionIncomeInvoiceDataModel
                = $sm->get('FcFlightManagement\Model\PermissionIncomeInvoiceDataModel');
        }

        return $this->permissionIncomeInvoiceDataModel;
    }

    /**
     * @return array|PermissionOutcomeInvoiceSearchModel|object
     */
    public function getPermissionOutcomeInvoiceSearchModel()
    {
        if (!$this->permissionOutcomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->permissionOutcomeInvoiceSearchModel =
                $sm->get('FcFlightManagement\Model\PermissionOutcomeInvoiceSearchModel');
        }

        return $this->permissionOutcomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\PermissionOutcomeInvoiceMainModel|object
     */
    public function getPermissionOutcomeInvoiceMainModel()
    {
        if (!$this->permissionOutcomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->permissionOutcomeInvoiceMainModel
                = $sm->get('FcFlightManagement\Model\PermissionOutcomeInvoiceMainModel');
        }

        return $this->permissionOutcomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\PermissionOutcomeInvoiceDataModel|object
     */
    public function getPermissionOutcomeInvoiceDataModel()
    {
        if (!$this->permissionOutcomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->permissionOutcomeInvoiceDataModel
                = $sm->get('FcFlightManagement\Model\PermissionOutcomeInvoiceDataModel');
        }

        return $this->permissionOutcomeInvoiceDataModel;
    }
}
