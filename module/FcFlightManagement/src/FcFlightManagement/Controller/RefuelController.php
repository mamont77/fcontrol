<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\ApServiceForm;
use FcFlight\Controller\FlightController;
use FcFlightManagement\Form\RefuelIncomeInvoiceStep1Form;
use FcFlightManagement\Form\RefuelOutcomeInvoiceStep1Form;
use FcFlightManagement\Model\RefuelIncomeInvoiceSearchModel;
use FcFlightManagement\Model\RefuelOutcomeInvoiceSearchModel;
use DOMPDFModule\View\Model\PdfModel;

/**
 * Class RefuelController
 * @package FcFlightManagement\Controller
 */
class RefuelController extends FlightController
{
    /**
     * Fields for search
     *
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
        'typeOfInvoice',
    );

    /**
     * @var \FcFlightManagement\Model\RefuelIncomeInvoiceSearchModel
     */
    protected $refuelIncomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\RefuelIncomeInvoiceMainModel
     */
    protected $refuelIncomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\RefuelIncomeInvoiceDataModel
     */
    protected $refuelIncomeInvoiceDataModel;

    /**
     * @var \FcFlightManagement\Model\RefuelOutcomeInvoiceSearchModel
     */
    protected $refuelOutcomeInvoiceSearchModel;

    /**
     * @var \FcFlightManagement\Model\RefuelOutcomeInvoiceMainModel
     */
    protected $refuelOutcomeInvoiceMainModel;

    /**
     * @var \FcFlightManagement\Model\RefuelOutcomeInvoiceDataModel
     */
    protected $refuelOutcomeInvoiceDataModel;


    /**
     * @return ViewModel
     */
    public function incomeInvoiceStep1Action()
    {
        $result = array();
        $searchForm = new RefuelIncomeInvoiceStep1Form('refuelIncomeInvoiceStep1',
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

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\RefuelIncomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getRefuelIncomeInvoiceSearchModel()->findByParams($data);

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

            $result = $this->getRefuelIncomeInvoiceSearchModel()->findByParams($data);
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
            $invoiceId = $this->getRefuelIncomeInvoiceMainModel()->add($data);

            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $row['preInvoiceRefuelId'] = $row['refuelId'];
                $this->getRefuelIncomeInvoiceDataModel()->add($row);
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

        $header = $this->getRefuelIncomeInvoiceMainModel()->get($invoiceId);
        $data = $this->getRefuelIncomeInvoiceDataModel()->getByInvoiceId($invoiceId);

        foreach ($data as $row) {
            $header->data[$row->refuelId] = $row;
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
        $searchForm = new RefuelOutcomeInvoiceStep1Form('refuelOutcomeInvoiceStep1',
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
                return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
            }

            $filter = $this->getServiceLocator()->get('FcFlightManagement\Filter\RefuelOutcomeInvoiceStep1Filter');
            $searchForm->setInputFilter($filter->getInputFilter());

            $searchForm->setData($request->getPost());
            if ($searchForm->isValid() && !$postIsEmpty) {
                $data = $searchForm->getData();
                $result = $this->getRefuelOutcomeInvoiceSearchModel()->findByParams($data);

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
    public function outcomeInvoiceStep2Action()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
        }

        $units = array();
        $unitsObj = $this->getUnits();
        foreach ($unitsObj as $unit) {
            $units[$unit->id] = $unit->name;
        }

        $currencies = new ApServiceForm(null, array());
        $currencies = $currencies->getCurrencyExchangeRate();

        $data = $request->getPost();

        if (empty($data['refuelsSelected'])) {
            $this->flashMessenger()->addErrorMessage('Result not found. Enter one or more fields.');
            return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
        }

        $result = $this->getRefuelOutcomeInvoiceSearchModel()->findByParams($data);

        $customerId = null;
        foreach ($result as $row) {
            $customerId = $row->incomeInvoiceAgentId;
            break;
        }
        $newInvoiceNumber = $this->getRefuelOutcomeInvoiceMainModel()->generateNewInvoiceNumber($customerId);

        return array(
            'newInvoiceNumber' => $newInvoiceNumber,
            'currencies' => $currencies,
            'units' => $units,
            'result' => $result,
        );
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceStep3Action()
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
    public function outcomeInvoiceAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
//            \Zend\Debug\Debug::dump($data);
            $invoiceId = $this->getRefuelOutcomeInvoiceMainModel()->add($data);

            foreach ($data['data'] as $row) {
                $row['invoiceId'] = $invoiceId;
                $this->getRefuelOutcomeInvoiceDataModel()->add($row);
            }

            $message = "Refuel outcome invoice was successfully added.";
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('management/refuel/outcome-invoice-show',
                array(
                    'id' => $invoiceId,
                ));
        }

        return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
    }

    /**
     * @return ViewModel
     */
    public function outcomeInvoiceShowAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
        }

        $header = $this->getRefuelOutcomeInvoiceMainModel()->get($invoiceId);
        $data = $this->getRefuelOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId);

        foreach ($data as $row) {
            $header->data[$row->refuelId] = $row;
        }
//        \Zend\Debug\Debug::dump($header);

        return new ViewModel(array(
            'header' => $header,
        ));

    }

    /**
     * @return PdfModel
     */
    public function outcomeInvoicePrintAction()
    {
        $invoiceId = (string)$this->params()->fromRoute('id', '');

        if (empty($invoiceId)) {
            return $this->redirect()->toRoute('management/refuel/outcome-invoice-step1');
        }

        $header = $this->getRefuelOutcomeInvoiceMainModel()->get($invoiceId);
        if (is_null($header->invoiceCustomerTermOfPayment)) {
            $header->invoiceCustomerTermOfPayment = 5;
        }
        $header->dueDate = \DateTime::createFromFormat('d-m-Y', $header->invoiceDate)
            ->add(new \DateInterval('P' . $header->invoiceCustomerTermOfPayment . 'D'))->format('d-m-Y');

        $data = $this->getRefuelOutcomeInvoiceDataModel()->getByInvoiceId($invoiceId);

        foreach ($data as $row) {
            $header->data[] = $row;
        }

        $pdf = new PdfModel();
//        $pdf = new ViewModel();
//        \Zend\Debug\Debug::dump($header);
        $pdf->setOption('filename', 'OR_' . $header->outcomeInvoiceMainCustomerShortName
            . '_' . $header->outcomeInvoiceMainNumber); // Triggers PDF download, automatically appends ".pdf"
        $pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
        $pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"

        // To set view variables
        $pdf->setVariables(array(
            'header' => $header,
        ));

        return $pdf;
    }

    /**
     * Get RefuelIncomeInvoiceSearchModel
     *
     * @return array|RefuelIncomeInvoiceSearchModel|object
     */
    public function getRefuelIncomeInvoiceSearchModel()
    {
        if (!$this->refuelIncomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->refuelIncomeInvoiceSearchModel = $sm->get('FcFlightManagement\Model\RefuelIncomeInvoiceSearchModel');
        }

        return $this->refuelIncomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\RefuelIncomeInvoiceMainModel|object
     */
    public function getRefuelIncomeInvoiceMainModel()
    {
        if (!$this->refuelIncomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->refuelIncomeInvoiceMainModel = $sm->get('FcFlightManagement\Model\RefuelIncomeInvoiceMainModel');
        }

        return $this->refuelIncomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\RefuelIncomeInvoiceDataModel|object
     */
    public function getRefuelIncomeInvoiceDataModel()
    {
        if (!$this->refuelIncomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->refuelIncomeInvoiceDataModel = $sm->get('FcFlightManagement\Model\RefuelIncomeInvoiceDataModel');
        }

        return $this->refuelIncomeInvoiceDataModel;
    }

    /**
     * @return array|RefuelOutcomeInvoiceSearchModel|object
     */
    public function getRefuelOutcomeInvoiceSearchModel()
    {
        if (!$this->refuelOutcomeInvoiceSearchModel) {
            $sm = $this->getServiceLocator();
            $this->refuelOutcomeInvoiceSearchModel =
                $sm->get('FcFlightManagement\Model\RefuelOutcomeInvoiceSearchModel');
        }

        return $this->refuelOutcomeInvoiceSearchModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\RefuelOutcomeInvoiceMainModel|object
     */
    public function getRefuelOutcomeInvoiceMainModel()
    {
        if (!$this->refuelOutcomeInvoiceMainModel) {
            $sm = $this->getServiceLocator();
            $this->refuelOutcomeInvoiceMainModel = $sm->get('FcFlightManagement\Model\RefuelOutcomeInvoiceMainModel');
        }

        return $this->refuelOutcomeInvoiceMainModel;
    }

    /**
     * @return array|\FcFlightManagement\Model\RefuelOutcomeInvoiceDataModel|object
     */
    public function getRefuelOutcomeInvoiceDataModel()
    {
        if (!$this->refuelOutcomeInvoiceDataModel) {
            $sm = $this->getServiceLocator();
            $this->refuelOutcomeInvoiceDataModel = $sm->get('FcFlightManagement\Model\RefuelOutcomeInvoiceDataModel');
        }

        return $this->refuelOutcomeInvoiceDataModel;
    }
}
