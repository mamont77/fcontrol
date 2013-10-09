<?php
/**
 * LogPlugin Helper for FcFlight module.
 * @author Ruslan Piskarev
 */

namespace FcFlight\Controller\Plugin;

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
     * @var string
     */
    protected $userName;

    /**
     * @param $data
     */
    public function setOldLogRecord($data)
    {
        $this->oldRecord = $data;
    }

    /**
     * @param $data
     */
    public function setNewLogRecord($data)
    {
        $this->newRecord = $data;

        return $this;
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

        return $this;
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

    /**
     * @param $userName
     * @return $this
     */
    public function setCurrentUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get the display name of the user
     *
     * @return string
     */
    public function getCurrentUserName()
    {
        return $this->userName;
    }
}
