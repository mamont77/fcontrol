<?php

namespace FcLibrariesSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibrariesSearch\Form\AdvancedSearchForm;

class SearchController extends AbstractActionController
{
    /**
     * @var
     */
    protected $searchModel;

    /**
     * @return array
     */
    public function advancedSearchAction()
    {
        $form = new AdvancedSearchForm();
        $result = array();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $filter = $this->getServiceLocator()->get('FcLibrariesSearch\Filter\AdvancedSearchFilter');

            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
//                \Zend\Debug\Debug::dump($data);
//                \Zend\Debug\Debug::dump($data->text);
//                \Zend\Debug\Debug::dump($data->library);
                $result = $this->getSearchModel()->getAdvancedSearchResult($data['text'], $data['library']);
            }
        }

        return array(
            'form' => $form,
            'result' => $result,
        );
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
