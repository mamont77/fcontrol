<?php

namespace FcLibraries\Model;
use Zend\Db\TableGateway\TableGateway;

class Region implements ModelInterface
{
    public $id;
    public $name;
    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
    }

    public function fetchAll()
    {

    }

    public function get($id)
    {

    }

    public function add($data)
    {
        $data = array(
            'name' => $data->name,
        );
        $this->_tableGateway->insert($data);
    }

    public function edit($data)
    {

    }

    public function delete($id)
    {

    }

}
