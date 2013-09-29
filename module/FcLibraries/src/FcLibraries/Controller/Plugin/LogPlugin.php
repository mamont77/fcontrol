<?php

namespace FcLibraries\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class LogPlugin
 * @package FcLibraries\Controller\Plugin
 */
class LogPlugin extends AbstractPlugin
{
    /**
     * @var array
     */
    protected $oldRecord = array();

    /**
     * @var array
     */
    protected $newRecord = array();

    /**
     * @var string
     */
    protected $logMessage;

    /**
     * @param $data
     */
    public function setOldLogRecord($data)
    {
        $this->oldRecord = array(
            'id' => $data->id,
            'Type Aircraft' => $data->aircraft_type_name,
            'Reg Number' => $data->reg_number,
        );
    }

    /**
     * @param $data
     */
    public function setNewLogRecord($data)
    {
        $this->newRecord = array(
            'id' => $data->id,
            'Type Aircraft' => $data->aircraft_type_name,
            'Reg Number' => $data->reg_number,
        );
    }

    /**
     * @param $data
     */
    public function setLogMessage($message)
    {
        $this->logMessage = array(
            'description' => $message,
            'data' => array(
                'old' => $this->getOldLogRecord(),
                'new' => $this->getNewLogRecord(),
            ),
        );
        $this->logMessage = serialize($this->logMessage);
    }

    /**
     * @return array
     */
    public function getOldLogRecord()
    {
        return $this->oldRecord;
    }

    /**
     * @return array
     */
    public function getNewLogRecord()
    {
        return $this->newRecord;
    }

    /**
     * @return array
     */
    public function getLogMessage()
    {
        return $this->logMessage;
    }
}
