<?php
/**
 * @namespace
 */
namespace FcLogEvents\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use FcLogEvents\Form\SearchForm;

/**
 * Class SearchController
 * @package FcLogEvents\Controller
 */
class SearchController extends AbstractActionController
{
    /**
     * @var
     */
    protected $searchModel;

    /**
     * @var
     */
    protected $userModel;

    /**
     * @return ViewModel
     */
    public function searchResultAction()
    {

        $select = new Select();
        $userList = $this->getUserModel()->fetchAll($select->order('username ' . Select::ORDER_ASCENDING));

        $result = '';
        $searchForm = new SearchForm('logsSearch',
            array(
                'usersList' => $userList
            ));

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            if ($data->dateFrom == '' && $data->dateTo == '' && $data->priority == '' && $data->username == '') {
                $result = 'Result not found. Enter one or more fields.';
            } else {
                $filter = $this->getServiceLocator()->get('FcLogEvents\Filter\SearchFilter');
                $searchForm->setInputFilter($filter->getInputFilter());
                $searchForm->setData($request->getPost());
                if ($searchForm->isValid()) {
                    $data = $searchForm->getData();
                    $filter->exchangeArray($data);
                    $result = $this->getSearchModel()->findSearchResult($filter);
                    if (count($result) == 0) {
                        $result = 'Result not found!';
                    }
                }
            }
        }

        return new ViewModel(array(
            'data' => $result,
            'searchForm' => $searchForm,
            'route' => 'flightsSearch',
        ));
    }

    /**
     * @return array|object
     */
    public function getSearchModel()
    {
        if (!$this->searchModel) {
            $sm = $this->getServiceLocator();
            $this->searchModel = $sm->get('FcLogEvents\Model\SearchModel');
        }
        return $this->searchModel;
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
