<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Model;

use FcFlight\Form\BaseForm;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

/**
 * Class PermissionIncomeInvoiceMainModel
 * @package FcFlightManagement\Model
 */
class PermissionIncomeInvoiceMainModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'invoiceIncomePermissionMain';

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

        $fields = array_flip($this->permissionIncomeInvoiceMainTableFieldsMap);

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

    public function get($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from($this->table);
        $select->columns($this->permissionIncomeInvoiceMainTableFieldsMap);

        $select->join(
            array('invoiceAgent' => 'library_kontragent'),
            $this->table . '.agentId = invoiceAgent.id',
            array(
                'invoiceAgentName' => 'name',
                'invoiceAgentShortName' => 'short_name',
            ),
            'left');

        $select->where(array($this->table . '.id' => $id));

        $resultSet = $this->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

//        $row->invoiceDate = date('d-m-Y', $row->invoiceDate);

        return $row;
    }
}
