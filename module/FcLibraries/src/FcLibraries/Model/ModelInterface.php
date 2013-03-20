<?php

namespace FcLibraries\Model;
use Zend\Db\TableGateway\TableGateway;


interface ModelInterface
{

    public function __construct(TableGateway $tableGateway);

    public function fetchAll();

    public function get($id);

    public function existName($id);

//    public function add(Region $data);

//    public function update(Region $data);

    public function remove($id);

}
