<?php

namespace FcLibraries\Model;

interface ModelInterface
{

    public function fetchAll();

    public function get($id);

    public function add(Library $library);

    public function edit(Library $library);

    public function delete($id);

}
