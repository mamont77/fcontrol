<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\ApServiceForm;
use FcFlight\Controller\FlightController;
use FcFlightManagement\Form\ApServiceIncomeInvoiceStep1Form;
use FcFlightManagement\Form\ApServiceOutcomeInvoiceStep1Form;
use FcFlightManagement\Model\ApServiceIncomeInvoiceSearchModel;
use FcFlightManagement\Model\ApServiceOutcomeInvoiceSearchModel;
use DOMPDFModule\View\Model\PdfModel;

/**
 * Class ApServiceController
 * @package FcFlightManagement\Controller
 */
class ApServiceController extends FlightController
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
        'airportId',
        'customerId',
        'airOperatorId',
        'typeOfInvoice',
    );

    /**
     * @var \FcFlightManagement\Model\ApServiceIncomeInvoiceSearchModel
     */
    protected $apServiceIncomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\ApServiceIncomeInvoiceMainModel
     */
    protected $apServiceIncomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\ApServiceIncomeInvoiceDataModel
     */
    protected $apServiceIncomeInvoiceDataModel;

    /**
     * @var \FcFlightManagement\Model\ApServiceOutcomeInvoiceSearchModel
     */
    protected $apServiceOutcomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\ApServiceOutcomeInvoiceMainModel
     */
    protected $apServiceOutcomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\ApServiceOutcomeInvoiceDataModel
     */
    protected $apServiceOutcomeInvoiceDataModel;


    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep1Action()
    {
        $result = array();
        $advancedDataWithIncomeInvoice = array();
        $advancedDataWithOutIncomeInvoice = array();

        $searchForm = new ApServiceIncomeInvoiceStep1Form('apServiceIncomeInvoiceStep1',
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
            foreach ($this->_searchMapFields as $field) {
                if (isset($data->$field) && !empty($data->$field)) {
                    $postIsEmpty = false;
                    continue;
                }
            }

            if ($postIsEmpty) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/ap-service/income-invoice-step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\ApServiceIncomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getApServiceIncomeInvoiceSearchModel()->findByParams($data);

                foreach ($result as $apServiceMain) {
                    if ($apServiceMain->incomeInvoiceMainId) {
                        $data = $this->getApServiceIncomeInvoiceDataModel()
                            ->getByInvoiceId($apServiceMain->incomeInvoiceMainId);
                        foreach ($data as $apServiceData) {
                            $advancedDataWithIncomeInvoice[$apServiceMain->incomeInvoiceMainId]['incomeInvoiceDataPriceTotal']
                                += $apServiceData->incomeInvoiceDataPriceTotal;
                            $advancedDataWithIncomeInvoice[$apServiceMain->incomeInvoiceMainId]['incomeInvoiceDataPriceTotalExchangedToUsd']
                                += $apServiceData->incomeInvoiceDataPriceTotalExchangedToUsd;
                        }
                    }
                }

                foreach ($result as $apServiceMain) {
                    if (!$apServiceMain->incomeInvoiceMainId) {
                        $legs = $this->getLegModel()->getByHeaderId($apServiceMain->preInvoiceHeaderId);

                        $currentLegId = $apServiceMain->legId;
                        $nextLegs = array();
                        foreach ($legs as $leg) {
                            if ($leg['id'] > $currentLegId) {
                                $nextLegs = $leg;
                                break;
                            }

                        }
                        if (count($nextLegs)) {
                            $advancedDataWithOutIncomeInvoice[$apServiceMain->legId]['legDepToNextAirportTime']
                                = (string)\DateTime::createFromFormat('d-m-Y', $nextLegs['dateOfFlight'])
                                ->setTime(0, 0)->getTimestamp();
                            $advancedDataWithOutIncomeInvoice[$apServiceMain->legId]['legDepToNextAirportICAO']
                                = $nextLegs['apDepIcao'];
                            $advancedDataWithOutIncomeInvoice[$apServiceMain->legId]['legDepToNextAirportIATA']
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
        $units = $this->getApServiceUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfApServices();
        foreach ($typeOfServicesObj as $typeOfService) {
            $typeOfServices[$typeOfService->id] = $typeOfService->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (empty($data['rowsSelected'])) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/ap-service/income-invoice-step1');
            }

            $result = $this->getApServiceIncomeInvoiceSearchModel()->findByParams($data)->current();
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
        $units = $this->getApServiceUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfApServices();
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

        return $this->redirect()->toRoute('management/ap-service/income-invoice-step1');
    }

    /**
     * @return mixed
     */
    public function incomeInvoiceAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $invoiceId = $this->getApServiceIncomeInvoiceMainModel()->add($data);

            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getApServiceIncomeInvoiceDataModel()->add($row);
            }

            $message = "AP Service income invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/ap-service/income-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/ap-service/income-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function incomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/ap-service/income-invoice-step1');
        }

        $header = $this->getApServiceIncomeInvoiceMainModel()->get($invoiceId);
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

        $data = $this->getApServiceIncomeInvoiceDataModel()->getByInvoiceId($invoiceId);

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

        $searchForm = new ApServiceOutcomeInvoiceStep1Form('apServiceOutcomeInvoiceStep1',
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
            foreach ($this->_searchMapFields as $field) {
                if (isset($data->$field) && !empty($data->$field)) {
                    $postIsEmpty = false;
                    continue;
                }
            }

            if ($postIsEmpty) {
                $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
                return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\ApServiceOutcomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getApServiceOutcomeInvoiceSearchModel()->findByParams($data);

                foreach ($result as $row) {
                    // Get info from income invoices
                    $data = $this->getApServiceIncomeInvoiceDataModel()->getByInvoiceId($row->incomeInvoiceMainId);
                    foreach ($data as $item) {
                        $incomeInvoiceData[$row->incomeInvoiceMainId]['incomeInvoiceDataPriceTotal']
                            += $item->incomeInvoiceDataPriceTotal;
                        $incomeInvoiceData[$row->incomeInvoiceMainId]['incomeInvoiceDataPriceTotalExchangedToUsd']
                            += $item->incomeInvoiceDataPriceTotalExchangedToUsd;
                    }

                    // Get info from outcome invoices
                    if ($row->outcomeInvoiceMainId) {
                        $data = $this->getApServiceOutcomeInvoiceDataModel()
                            ->getByInvoiceId($row->outcomeInvoiceMainId, false);
                        foreach ($data as $item) {
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotal']
                                += $item->outcomeInvoiceDataPriceTotal;
                            $outcomeInvoiceData[$row->outcomeInvoiceMainId]['outcomeInvoiceDataPriceTotalExchangedToUsd']
                                += $item->outcomeInvoiceDataPriceTotalExchangedToUsd;
                        }

                        $data = $this->getApServiceOutcomeInvoiceDataModel()
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
            return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
        }

        $units = $this->getApServiceUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfApServices();
        foreach ($typeOfServicesObj as $typeOfService) {
            $typeOfServices[$typeOfService->id] = $typeOfService->name;
        }
        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $data = $request->getPost();

        if (empty($data['rowsSelected'])) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
        }

        $result = $this->getApServiceOutcomeInvoiceSearchModel()->findByParams($data)->current();
        $newInvoiceNumber = $this->getApServiceOutcomeInvoiceMainModel()->generateNewInvoiceNumber($result->flightAgentId);

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

        $incomeInvoiceData = $this->getApServiceIncomeInvoiceDataModel()->getByInvoiceId($result->incomeInvoiceMainId);

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
            return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
        }

        $units = $this->getApServiceUnits();
        $typeOfServices = array();
        $typeOfServicesObj = $this->getTypeOfApServices();
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

            $invoiceId = $this->getApServiceOutcomeInvoiceMainModel()->add($data);
            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getApServiceOutcomeInvoiceDataModel()->add($row, false);
            }
            foreach ($data['subData'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getApServiceOutcomeInvoiceDataModel()->add($row, true);
            }

            $message = "AP Service outcome invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/ap-service/outcome-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/ap-service/outcome-invoice-step1');
        }

        $header = $this->getApServiceOutcomeInvoiceMainModel()->get($invoiceId);
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

        $data = $this->getApServiceOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId, false);
        foreach ($data as $row) {
            $header->data[$row->outcomeInvoiceDataId] = $row;
        }
        $subData = $this->getApServiceOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId, true);
        foreach ($subData as $row) {
            $header->subData[$row->outcomeInvoiceDataId] = $row;
        }

        return new ViewModel(array(
            'header' => $header,
        ));
    }

    public function outcomeInvoicePrintAction()
    {
        require_once (__DIR__ . '/../../../../../vendor/dompdf/dompdf_config.inc.php');

        $html =
            '<html><body>'.
            '<p>Put your html here, or<b> generate</b> it with your favourite '.
            'templating system.</p>'.
            '<table></table>'.
            '</body></html>';
        $pdf = new \DOMPDF();
        $pdf->load_html($html);
        $pdf->render();
        $pdf->stream("sample.pdf");
    }

    /**
     * Get ApServiceIncomeInvoiceSearchModel
     *
     * @return array|ApServiceIncomeInvoiceSearchModel|object
     */
    public function getApServiceIncomeInvoiceSearchModel()
    {
        if (!$this->apServiceIncomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceIncomeInvoiceSearchModel
                = $sm->get('FcFlightManagement\Model\ApServiceIncomeInvoiceSearchModel');
        }

        return $this->apServiceIncomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\ApServiceIncomeInvoiceMainModel|object
     */
    public function getApServiceIncomeInvoiceMainModel()
    {
        if (!$this->apServiceIncomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceIncomeInvoiceMainModel
                = $sm->get('FcFlightManagement\Model\ApServiceIncomeInvoiceMainModel');
        }

        return $this->apServiceIncomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\ApServiceIncomeInvoiceDataModel|object
     */
    public function getApServiceIncomeInvoiceDataModel()
    {
        if (!$this->apServiceIncomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceIncomeInvoiceDataModel
                = $sm->get('FcFlightManagement\Model\ApServiceIncomeInvoiceDataModel');
        }

        return $this->apServiceIncomeInvoiceDataModel;
    }

    /**
     * @return array|ApServiceOutcomeInvoiceSearchModel|object
     */
    public function getApServiceOutcomeInvoiceSearchModel()
    {
        if (!$this->apServiceOutcomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceOutcomeInvoiceSearchModel =
                $sm->get('FcFlightManagement\Model\ApServiceOutcomeInvoiceSearchModel');
        }

        return $this->apServiceOutcomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\ApServiceOutcomeInvoiceMainModel|object
     */
    public function getApServiceOutcomeInvoiceMainModel()
    {
        if (!$this->apServiceOutcomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceOutcomeInvoiceMainModel
                = $sm->get('FcFlightManagement\Model\ApServiceOutcomeInvoiceMainModel');
        }

        return $this->apServiceOutcomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\ApServiceOutcomeInvoiceDataModel|object
     */
    public function getApServiceOutcomeInvoiceDataModel()
    {
        if (!$this->apServiceOutcomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->apServiceOutcomeInvoiceDataModel
                = $sm->get('FcFlightManagement\Model\ApServiceOutcomeInvoiceDataModel');
        }

        return $this->apServiceOutcomeInvoiceDataModel;
    }

    /**
     * @return array
     */
    public function getApServiceUnits()
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
