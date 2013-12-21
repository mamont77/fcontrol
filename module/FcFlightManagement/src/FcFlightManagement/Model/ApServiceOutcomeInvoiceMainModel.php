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
 * Class ApServiceOutcomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class ApServiceOutcomeInvoiceMainModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceOutcomeApServiceMain';

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
        $data['dateArr'] = \DateTime::createFromFormat('d-m-Y', $data['dateArr'])->setTime(0, 0, 0)->getTimestamp();
        $data['dateDep'] = \DateTime::createFromFormat('d-m-Y', $data['dateDep'])->setTime(0, 0, 0)->getTimestamp();

        $fields = array_flip($this->apServiceOutcomeInvoiceMainTableFieldsMap);

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
        $select->columns($this->apServiceOutcomeInvoiceMainTableFieldsMap);

        $select->join(array('outcomeInvoiceMainTypeOfService' => 'library_type_of_ap_service'),
            $this->table . '.typeOfServiceId = outcomeInvoiceMainTypeOfService.id',
            array('outcomeInvoiceMainTypeOfServiceName' => 'name'),
            'left'
        );

        $select->join(array('incomeInvoiceMain' => $this->apServiceIncomeInvoiceMainTableName),
            $this->table . '.incomeInvoiceId = incomeInvoiceMain.id',
            $this->apServiceIncomeInvoiceMainTableFieldsMap,
            'left'
        );

        $select->join(array('preIncomeInvoiceMain' => $this->apServicePreInvoiceMainTableName),
            'incomeInvoiceMain.preInvoiceId = preIncomeInvoiceMain.id',
            $this->apServicePreInvoiceTableFieldsMap,
            'left'
        );

        $select->join(array('flight' => $this->flightTableName),
            'preIncomeInvoiceMain.headerId = flight.id',
            $this->flightTableFieldsMap,
            'left'
        );

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        $row->outcomeInvoiceMainDate = date('d-m-Y', $row->outcomeInvoiceMainDate);
        $row->outcomeInvoiceMainDateArr = date('d-m-Y', $row->outcomeInvoiceMainDateArr);
        $row->outcomeInvoiceMainDateDep = date('d-m-Y', $row->outcomeInvoiceMainDateDep);

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
        $lastInvoiceId = (int)$row->invoiceId + 1;
        if (!$row) {
            $lastInvoiceId = 1;
        }

        return sprintf('%04d', $customerId) . '-' . sprintf('%08d', $lastInvoiceId);
    }
}
