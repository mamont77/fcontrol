<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class RefuelOutcomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class RefuelOutcomeInvoiceMainModel extends AbstractTableGateway
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomeRefuelMain';

    /**
     * @var array
     */
    protected $_tableFields = array(
        'invoiceId' => 'invoiceId',
        'invoiceNumber' => 'invoiceNumber',
        'invoiceDate' => 'invoiceDate',
        'invoiceCurrency' => 'invoiceCurrency',
        'invoiceExchangeRate' => 'invoiceExchangeRate',
        'invoiceRefuelSupplierId' => 'invoiceRefuelSupplierId',
        'invoiceStatus' => 'invoiceStatus',
    );

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }

    /**
     * @param $data
     * @return int
     */
    public function add($data)
    {
        $invoiceDate = \DateTime::createFromFormat('d-m-Y', $data['invoiceDate']);
        $data['invoiceDate'] = $invoiceDate->setTime(0, 0, 0)->getTimestamp();

        $data = array(
            'invoiceNumber' => (string)$data['invoiceNumber'],
            'invoiceDate' => (int)$data['invoiceDate'],
            'invoiceCurrency' => (string)$data['invoiceCurrency'],
            'invoiceExchangeRate' => (string)$data['invoiceExchangeRate'],
            'invoiceRefuelSupplierId' => (int)$data['invoiceRefuelSupplierId'],
            'invoiceStatus' => 1,
        );

        $this->insert($data);

        return $this->getLastInsertValue();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function get($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns($this->_tableFields);

        $select->join(array('libraryKontragent' => 'library_kontragent'),
            'libraryKontragent.id = ' . $this->table . '.invoiceRefuelSupplierId',
            array('invoiceRefuelSupplierName' => 'short_name'), 'left');

        $select->where(array($this->table . '.invoiceId' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->invoiceDate = date('d-m-Y', $row->invoiceDate);

        return $row;
    }

    /**
     * @param $customerId
     * @return string
     */
    public function generateNewInvoiceNumber($customerId)
    {
        $customerId = (int)$customerId;
        $select = $this->getSql()->select();
        $select->order(array('invoiceId ' . $select::ORDER_DESCENDING));
        $select->limit(1);
        $row = $this->selectWith($select)->current();
        $lastInvoiceId = (int)$row->invoiceId + 1;
        if (!$row) {
            $lastInvoiceId = 1;
        }

        return sprintf('%04d', $customerId) . '-' . sprintf('%08d', $lastInvoiceId);
    }
}
