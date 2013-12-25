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
 * Class PermissionIncomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class PermissionIncomeInvoiceDataModel extends PermissionIncomeInvoiceMainModel
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomePermissionData';

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
        $fields = array_flip($this->permissionIncomeInvoiceDataTableFieldsMap);

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

    public function getByInvoiceId($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->permissionIncomeInvoiceDataTableFieldsMap);
//
//        $select->join(
//            array('incomeInvoiceDataTypeOfService' => 'library_type_of_ap_service'),
//            $this->table . '.typeOfServiceId = incomeInvoiceDataTypeOfService.id',
//            array(
//                'incomeInvoiceDataTypeOfServiceName' => 'name',
//            ),
//            'left');

        $select->where(array($this->table . '.invoiceId' => $id));
        $select->order(array($this->table . '.id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

//        \Zend\Debug\Debug::dump($resultSet);
//
//        foreach ($resultSet as $row) {
//            \Zend\Debug\Debug::dump($row);
//
//        }

        return $resultSet;
    }
}
