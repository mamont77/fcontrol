<?php
/**
 * @namespace
 */
namespace FcFlightManagement\Controller;

use FcFlightManagement\Form\RefuelStep1Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Controller\FlightController;
use FcFlight\Model\FlightHeaderModel;
use FcFlight\Model\LegModel;
use FcFlight\Model\PermissionModel;
use FcFlight\Model\RefuelModel;
use FcFlight\Model\ApServiceModel;
use FcFlightManagement\Form\RefuelForm;

/**
 * Class RefuelController
 * @package FcFlightManagement\Controller
 */
class RefuelController extends FlightController
{
    public function indexAction()
    {

        print 1;
    }

    /**
     * @return ViewModel
     */
    public function findStep1Action()
    {

        $form = new RefuelStep1Form('managementRefuelStep1',
            array(
                'libraries' => array(
                    'aircrafts' => $this->getAircrafts(),
                    'agents' => $this->getKontragents(),
                    'airports' => $this->getAirports(),
                    'customers' => $this->getKontragents(),
                    'airOperators' => $this->getAirOperators(),
                )
            )
        );

        return array(
            'form' => $form
        );
    }
}
