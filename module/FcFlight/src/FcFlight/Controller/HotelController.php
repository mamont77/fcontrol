<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\HotelForm;

/**
 * Class HotelController
 * @package FcFlight\Controller
 */
class HotelController extends FlightController
{
    /**
     * @var int
     */
    protected $headerId;

    /**
     * @var array
     */
    protected $dataForLogger = array();

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {


    }

    /**
     * @return array
     */
    public function editAction()
    {

    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {

    }

    /**
     * @param $data
     */
    protected function setDataForLogger($data)
    {
        $this->dataForLogger = array(
            'id' => $data->id,
            'Airport' => $data->airportName . ' (' . $data->icao . '/' . $data->iata . ')',
            '' => ($data->isNeed) ? 'YES' : 'NO',
            'Agent' => $data->agentName,
        );
    }
}
