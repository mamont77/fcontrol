<?php

namespace FcLibraries\Model;

//use Zend\Db\TableGateway\TableGateway;

class Library implements ModelInterface
{
//    protected $tableGateway;
//
//    public function __construct(TableGateway $tableGateway)
//    {
//        $this->tableGateway = $tableGateway;
//    }

    public function fetchAll()
    {
//        $resultSet = $this->tableGateway->select();
//        return $resultSet;
    }

    public function get($id)
    {
//        $id  = (int) $id;
//        $rowset = $this->tableGateway->select(array('id' => $id));
//        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $id");
//        }
//        return $row;
    }

    public function add(Library $library)
    {
//        $data = array(
//            'artist' => $album->artist,
//            'title'  => $album->title,
//        );
//
//        $id = (int)$album->id;
//        if ($id == 0) {
//            $this->tableGateway->insert($data);
//        } else {
//            if ($this->getAlbum($id)) {
//                $this->tableGateway->update($data, array('id' => $id));
//            } else {
//                throw new \Exception('Form id does not exist');
//            }
//        }
    }

    public function edit(Library $library)
    {
//        $data = array(
//            'artist' => $album->artist,
//            'title'  => $album->title,
//        );
//
//        $id = (int)$album->id;
//        if ($id == 0) {
//            $this->tableGateway->insert($data);
//        } else {
//            if ($this->getAlbum($id)) {
//                $this->tableGateway->update($data, array('id' => $id));
//            } else {
//                throw new \Exception('Form id does not exist');
//            }
//        }
    }

    public function delete($id)
    {
//        $this->tableGateway->delete(array('id' => $id));
    }
}
