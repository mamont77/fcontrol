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
 * Class ApServiceOutcomeInvoiceDataModel
 * @package FcFlightManagement\Model
 */
class ApServiceOutcomeInvoiceDataModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomeApServiceData';

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
     * @param bool $isAdditionalInfo
     * @return int
     */
    public function add($data, $isAdditionalInfo = false)
    {
        $data['isAdditionalInfo'] = ($isAdditionalInfo) ? 1 : 0;
        $fields = array_flip($this->apServiceOutcomeInvoiceDataTableFieldsMap);

        foreach ($fields as $key => &$field) {
            if (isset($data[$key])) {
                $field = $data[$key];
            } else {
                unset($fields[$key]);
            }
        }

//        \Zend\Debug\Debug::dump($fields);exit;

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

        $select->columns($this->apServiceOutcomeInvoiceDataTableFieldsMap);

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
