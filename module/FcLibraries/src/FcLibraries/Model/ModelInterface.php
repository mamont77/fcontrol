<?php

namespace FcLibraries\Model;
use Zend\Db\Adapter\Adapter;

interface ModelInterface
{

    public function __construct(Adapter $adapter);

    public function fetchAll();

    public function get($id);

//    public function add($data);
//
//    public function save($data);

    public function remove($id);

}
