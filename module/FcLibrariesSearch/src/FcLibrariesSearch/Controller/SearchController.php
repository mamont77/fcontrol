<?php

namespace FcLibrariesSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibrariesSearch\Form\AdvancedSearchForm;
use Zend\Db\Sql\Select;

class SearchController extends AbstractActionController
{
    /**
     * @var
     */
    protected $searchModel;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function advancedSearchAction()
    {
        $form = new AdvancedSearchForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = $this->getServiceLocator()->get('FcLibrariesSearch\Filter\AdvancedSearchFilter');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $this->getSearchModel()->advancedSearch($filter);
            }
        }

        return array('form' => $form);
    }

    /**
     * @return array|object
     */
    public function getSearchModel()
    {
        if (!$this->searchModel) {
            $sm = $this->getServiceLocator();
            $this->searchModel = $sm->get('FcLibrariesSearch\Model\SearchModel');
        }
        return $this->searchModel;
    }
}
