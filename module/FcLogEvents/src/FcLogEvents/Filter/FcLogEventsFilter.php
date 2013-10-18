<?php
/**
 * @namespace
 */
namespace FcLogEvents\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class FcLogEventsFilter
 * @package FcLogEvents\Filter
 */
class FcLogEventsFilter
{
    /**
     * @var string
     */
    protected $table = 'logs';

    public $id;
    public $message;
    public $priority;
    public $priorityName;
    public $username;
    public $url;
    public $ipaddress;
    public $timestamp;
    public $component;

    /**
     * @var
     */
    protected $inputFilter;

    /**
     * @var $dbAdapter
     */
    protected $dbAdapter;

    /**
     * @param \Zend\Db\Adapter\Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->message = (isset($data['message'])) ? $data['message'] : null;
        $this->priority = (isset($data['priority'])) ? $data['priority'] : null;
        $this->priorityName = (isset($data['priorityName'])) ? $data['priorityName'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->ipaddress = (isset($data['ipaddress'])) ? $data['ipaddress'] : null;
        $this->timestamp = (isset($data['timestamp'])) ? $data['timestamp'] : null;
        $this->component = (isset($data['component'])) ? $data['component'] : null;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return InputFilter|InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
