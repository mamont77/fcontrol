<?php

namespace FcLibraries\Controller;

/**
 * Interface ControllerInterface
 * @package FcLibraries\Controller
 */
interface ControllerInterface
{

    /**
     * @return mixed
     */
    public function indexAction();

    /**
     * @return mixed
     */
    public function addAction();

    /**
     * @return mixed
     */
    public function editAction();

    /**
     * @return mixed
     */
    public function deleteAction();
}
