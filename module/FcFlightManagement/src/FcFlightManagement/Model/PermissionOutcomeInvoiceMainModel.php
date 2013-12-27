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
 * Class PermissionOutcomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class PermissionOutcomeInvoiceMainModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomePermissionMain';

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

        $data['date'] = \DateTime::createFromFormat('d-m-Y', $data['date'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->permissionOutcomeInvoiceMainTableFieldsMap);

        foreach ($fields as $key => &$field) {
            if (isset($data[$key])) {
                $field = $data[$key];
            } else {
                unset($fields[$key]);
            }
        }

        $this->insert($fields);

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
        $select->columns($this->permissionOutcomeInvoiceMainTableFieldsMap);

        $select->join(
            array('outcomeInvoiceMainCustomer' => 'library_kontragent'),
            $this->table . '.customerId = outcomeInvoiceMainCustomer.id',
            array(
                'outcomeInvoiceMainCustomerName' => 'name',
                'outcomeInvoiceMainCustomerShortName' => 'short_name',
            ),
            'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->outcomeInvoiceMainDate = date('d-m-Y', $row->outcomeInvoiceMainDate);

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
        $select->order(array('id ' . $select::ORDER_DESCENDING));
        $select->limit(1);
        $row = $this->selectWith($select)->current();
        $lastInvoiceId = (int)$row->id + 1;
        if (!$row) {
            $lastInvoiceId = 1;
        }

        return sprintf('%04d', $customerId) . '-' . sprintf('%08d', $lastInvoiceId);
    }
}
