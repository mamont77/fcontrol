<?php

namespace FcLibraries\Model;

interface ModelInterface
{

    public function fetchAll();

    public function get($id);

    public function add($data);

    public function edit($data);

    public function delete($id);

}
