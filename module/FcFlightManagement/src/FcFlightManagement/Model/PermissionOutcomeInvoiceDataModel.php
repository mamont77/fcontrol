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
 * Class PermissionOutcomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class PermissionOutcomeInvoiceDataModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomePermissionData';

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
        $data['dateDep'] = \DateTime::createFromFormat('d-m-Y', $data['dateDep'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateArr'] = \DateTime::createFromFormat('d-m-Y', $data['dateArr'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->permissionOutcomeInvoiceDataTableFieldsMap);

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
     * @param bool $isAdditionalInfo
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByInvoiceId($id, $isAdditionalInfo = false)
    {
        $id = (int)$id;
        $isAdditionalInfo = ($isAdditionalInfo) ? 1 : 0;
        $select = new Select();
        $select->from($this->table);

        $select->columns($this->permissionOutcomeInvoiceDataTableFieldsMap);

        $select->join(array('outcomeInvoiceDataTypeOfService' => 'library_type_of_ap_service'),
            $this->table . '.typeOfServiceId = outcomeInvoiceDataTypeOfService.id',
            array('outcomeInvoiceDataTypeOfServiceName' => 'name'),
            'left'
        );

        $select->where(array(
            $this->table . '.invoiceId' => $id,
            $this->table . '.isAdditionalInfo' => $isAdditionalInfo,
        ));
        $select->order(array($this->table . '.id ' . $select::ORDER_ASCENDING));
//        \Zend\Debug\Debug::dump($select->getSqlString());

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
}
