<?php

namespace FcLogEvents\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
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
     * @return ViewModel
     */
    public function searchResultAction()
    {

        $result = '';
        $searchForm = new SearchForm();
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
}
