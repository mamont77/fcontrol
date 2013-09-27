<?php

namespace FcLogEvents\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

/**
 * Class IndexController
 * @package FcLogEvents\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @var
     */
    protected $logEventsModel;

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
        $itemsPerPage = 20;

        $data->current();
        $pagination = new Paginator(new paginatorIterator($data));
        $pagination->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(array(
            'order_by' => $order_by,
            'order' => $order,
            'page' => $page,
            'pagination' => $pagination,
            'route' => 'zfcadmin/logs',
//            'searchForm' => new SearchForm('librarySearch', array('library' => 'log_table')),
        ));
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
}
