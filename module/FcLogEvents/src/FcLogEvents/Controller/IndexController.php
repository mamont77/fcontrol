<?php
/**
 * @namespace
 */
namespace FcLogEvents\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use FcLogEvents\Form\SearchForm;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class IndexController
 * @package FcLogEvents\Controller
 */
class IndexController extends AbstractActionController
{

    /**
     * Period in day
     */
    const PERIOD = 128;

    /**
     * @var
     */
    protected $logEventsModel;

    /**
     * @var
     */
    protected $userModel;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'timestamp';
        $order = $this->params()->fromRoute('order') ?
            $this->params()->fromRoute('order') : Select::ORDER_DESCENDING;
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $data = $this->getFcLogEventsModel()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 50;

        $data->current();
        $pagination = new Paginator(new paginatorIterator($data));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        $select = new Select();
        $userList = $this->getUserModel()->fetchAll($select->order('username ' . Select::ORDER_ASCENDING));

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'logs',
            'searchForm' => new SearchForm(
                'logsSearch',
                array(
                    'usersList' => $userList
                )
            ),
        ));
    }

    /**
     * Remove old log data from DB.
     *
     * @return ViewModel
     */
    public function cronAction()
    {
        $this->getFcLogEventsModel()->cleaningOldData(self::PERIOD);

        $view = new ViewModel(array('data' => ''));
        $view->setTerminal(true);

        return $view;
    }

    /**
     * @return array|object
     */
    public function getFcLogEventsModel()
    {
        if (!$this->logEventsModel) {
            $sm = $this->getServiceLocator();
            $this->logEventsModel = $sm->get('FcLogEvents\Model\FcLogEventsModel');
        }
        return $this->logEventsModel;
    }

    /**
     * @return array|object
     */
    public function getUserModel()
    {
        if (!$this->userModel) {
            $sm = $this->getServiceLocator();
            $this->userModel = $sm->get('FcAdmin\Model\UserTable');
        }
        return $this->userModel;
    }
}
