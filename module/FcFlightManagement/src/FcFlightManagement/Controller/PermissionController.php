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

                foreach ($result as $permissionMain) {
                    if ($permissionMain->incomeInvoiceMainId) {
                        $data = $this->getPermissionIncomeInvoiceDataModel()
                            ->getByInvoiceId($permissionMain->incomeInvoiceMainId);
                        foreach ($data as $permissionData) {
                            $advancedDataWithIncomeInvoice[$permissionMain->incomeInvoiceMainId]['incomeInvoiceDataPriceTotal']
                                += $permissionData->incomeInvoiceDataPriceTotal;
                            $advancedDataWithIncomeInvoice[$permissionMain->incomeInvoiceMainId]['incomeInvoiceDataPriceTotalExchangedToUsd']
                                += $permissionData->incomeInvoiceDataPriceTotalExchangedToUsd;
                        }
                    }
                }

                foreach ($result as $permissionMain) {
                    if (!$permissionMain->incomeInvoiceMainId) {
                        $legs = $this->getLegModel()->getByHeaderId($permissionMain->preInvoiceHeaderId);

                        $currentLegId = $permissionMain->legId;
                        $nextLegs = array();
                        foreach ($legs as $leg) {
                            if ($leg['id'] > $currentLegId) {
                                $nextLegs = $leg;
                                break;
                            }

                        }
                        if (count($nextLegs)) {
                            $advancedDataWithOutIncomeInvoice[$permissionMain->legId]['legDepToNextAirportTime']
                                = (string)\DateTime::createFromFormat('d-m-Y', $nextLegs['dateOfFlight'])
                                ->setTime(0, 0)->getTimestamp();
                            $advancedDataWithOutIncomeInvoice[$permissionMain->legId]['legDepToNextAirportICAO']
                                = $nextLegs['apDepIcao'];
                            $advancedDataWithOutIncomeInvoice[$permissionMain->legId]['legDepToNextAirportIATA']
                                = $nextLegs['apDepIata'];
                        }

                    }
                }
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
        $units = $this->getPermissionUnits();
        $typeOfPermission = array();
        $typeOfPermissionObj = $this->getTypeOfPermissions();
        foreach ($typeOfPermissionObj as $type) {
            $typeOfPermission[$type->id] = $type->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (empty($data['rowsSelected'])) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/permission/income-invoice-step1');
            }

            $result = $this->getPermissionIncomeInvoiceSearchModel()->findByParams($data)->current();
            $result->legDepToNextAirportTime = '';
            $result->legDepToNextAirportICAO = '';
            $result->legDepToNextAirportIATA = '';
            $legs = $this->getLegModel()->getByHeaderId($result->preInvoiceHeaderId);

            $currentLegId = $result->legId;
            $nextLegs = array();
            foreach ($legs as $leg) {
                if ($leg['id'] > $currentLegId) {
                    $nextLegs = $leg;
                    break;
                }

            }
            if (count($nextLegs)) {
                $result->legDepToNextAirportTime = (string)\DateTime::createFromFormat('d-m-Y',
                    $nextLegs['dateOfFlight'])->setTime(0, 0)->getTimestamp();
                $result->legDepToNextAirportICAO = $nextLegs['apDepIcao'];
                $result->legDepToNextAirportIATA = $nextLegs['apDepIata'];
            }
        }

        return array(
            'currencies' => $currencies,
            'units' => $units,
            'typeOfServices' => $typeOfServices,
            'result' => $result,
        );
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep3Action()
    {
        $units = $this->getPermissionUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfPermissions();
        foreach ($typeOfServicesObj as $typeOfService) {
            $typeOfServices[$typeOfService->id] = $typeOfService->name;
        }

        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $result = $request->getPost();

            return array(
                'currencies' => $currencies,
                'units' => $units,
                'typeOfServices' => $typeOfServices,
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
        $header->legDepToNextAirportTime = '';
        $header->legDepToNextAirportICAO = '';
        $header->legDepToNextAirportIATA = '';
        $legs = $this->getLegModel()->getByHeaderId($header->preInvoiceHeaderId);
        $currentLegId = $header->legId;
        $nextLegs = array();
        foreach ($legs as $leg) {
            if ($leg['id'] > $currentLegId) {
                $nextLegs = $leg;
                break;
            }

        }
        if (count($nextLegs)) {
            $header->legDepToNextAirportTime = (string)\DateTime::createFromFormat('d-m-Y',
                $nextLegs['dateOfFlight'])->setTime(0, 0)->getTimestamp();
            $header->legDepToNextAirportICAO = $nextLegs['apDepIcao'];
            $header->legDepToNextAirportIATA = $nextLegs['apDepIata'];
        }

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

                foreach ($result as $row) {
                    // Get info from income invoices
                    $data = $this->getPermissionIncomeInvoiceDataModel()->getByInvoiceId($row->incomeInvoiceMainId);
                    foreach ($data as $item) {
                        $incomeInvoiceData[$row->incomeInvoiceMainId]['incomeInvoiceDataPriceTotal']
                            += $item->incomeInvoiceDataPriceTotal;
                        $incomeInvoiceData[$row->incomeInvoiceMainId]['incomeInvoiceDataPriceTotalExchangedToUsd']
                            += $item->incomeInvoiceDataPriceTotalExchangedToUsd;
                    }

                    // Get info from outcome invoices
                    if ($row->outcomeInvoiceMainId) {
                        $data = $this->getPermissionOutcomeInvoiceDataModel()
                            ->getByInvoiceId($row->outcomeInvoiceMainId, false);
                        foreach ($data as $item) {
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotal']
                                += $item->outcomeInvoiceDataPriceTotal;
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotalExchangedToUsd']
                                += $item->outcomeInvoiceDataPriceTotalExchangedToUsd;
                        }

                        $data = $this->getPermissionOutcomeInvoiceDataModel()
                            ->getByInvoiceId($row->outcomeInvoiceMainId, true);
                        foreach ($data as $item) {
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotal']
                                += $item->outcomeInvoiceDataPriceTotal;
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotalExchangedToUsd']
                                += $item->outcomeInvoiceDataPriceTotalExchangedToUsd;
                        }

                        $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotal']
                            += $row->disbursementTotal;
                        $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotalExchangedToUsd']
                            += $row->disbursementTotalExchangedToUsd;
                    }

                }
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

        $units = $this->getPermissionUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfPermissions();
        foreach ($typeOfServicesObj as $typeOfService) {
            $typeOfServices[$typeOfService->id] = $typeOfService->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $data = $request->getPost();

        if (empty($data['rowsSelected'])) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/permission/outcome-invoice-step1');
        }

        $result = $this->getPermissionOutcomeInvoiceSearchModel()->findByParams($data)->current();
        $newInvoiceNumber = $this->getPermissionOutcomeInvoiceMainModel()->generateNewInvoiceNumber($result->flightAgentId);

        $result->legDepToNextAirportTime = '';
        $result->legDepToNextAirportICAO = '';
        $result->legDepToNextAirportIATA = '';
        $legs = $this->getLegModel()->getByHeaderId($result->preInvoiceHeaderId);
        $currentLegId = $result->legId;
        $nextLegs = array();
        foreach ($legs as $leg) {
            if ($leg['id'] > $currentLegId) {
                $nextLegs = $leg;
                break;
            }

        }
        if (count($nextLegs)) {
            $result->legDepToNextAirportTime = (string)\DateTime::createFromFormat('d-m-Y',
                $nextLegs['dateOfFlight'])->setTime(0, 0)->getTimestamp();
            $result->legDepToNextAirportICAO = $nextLegs['apDepIcao'];
            $result->legDepToNextAirportIATA = $nextLegs['apDepIata'];
        }

        $incomeInvoiceData = $this->getPermissionIncomeInvoiceDataModel()->getByInvoiceId($result->incomeInvoiceMainId);

        return array(
            'newInvoiceNumber' => $newInvoiceNumber,
            'currencies' => $currencies,
            'typeOfServices' => $typeOfServices,
            'units' => $units,
            'result' => $result,
            'incomeInvoiceData' => $incomeInvoiceData,
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

        $units = $this->getPermissionUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfPermissions();
        foreach ($typeOfServicesObj as $typeOfService) {
            $typeOfServices[$typeOfService->id] = $typeOfService->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $result = $request->getPost();

        return array(
            'currencies' => $currencies,
            'typeOfServices' => $typeOfServices,
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
        $header->legDepToNextAirportTime = '';
        $header->legDepToNextAirportICAO = '';
        $header->legDepToNextAirportIATA = '';
        $legs = $this->getLegModel()->getByHeaderId($header->preInvoiceHeaderId);
        $currentLegId = $header->legId;
        $nextLegs = array();
        foreach ($legs as $leg) {
            if ($leg['id'] > $currentLegId) {
                $nextLegs = $leg;
                break;
            }

        }
        if (count($nextLegs)) {
            $header->legDepToNextAirportTime = (string)\DateTime::createFromFormat('d-m-Y',
                $nextLegs['dateOfFlight'])->setTime(0, 0)->getTimestamp();
            $header->legDepToNextAirportICAO = $nextLegs['apDepIcao'];
            $header->legDepToNextAirportIATA = $nextLegs['apDepIata'];
        }

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

    /**
     * @return array
     */
    public function getPermissionUnits()
    {
        return array(
            'Length' => 'Length',
            'Weight' => 'Weight',
            'Time' => 'Time',
            'Quantity' => 'Quantity',
            'Other' => 'Other',
        );
    }
}
