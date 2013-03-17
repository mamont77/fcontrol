<?php

namespace FcLibraries\Controller;

interface ControllerInterface
{

    public function indexAction();


    public function addAction();


    public function editAction();


    public function deleteAction();

    public function getModelTable();
}
