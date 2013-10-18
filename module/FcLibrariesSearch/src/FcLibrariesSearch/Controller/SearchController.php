<?php
/**
 * @namespace
 */
namespace FcLibrariesSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcLibrariesSearch\Form\AdvancedSearchForm;

/**
 * Class SearchController
 * @package FcLibrariesSearch\Controller
 */
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
        $library = '';

        $request = $this->getRequest();

        if ($request->isPost()) {

            $filter = $this->getServiceLocator()->get('FcLibrariesSearch\Filter\AdvancedSearchFilter');

            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $filter->exchangeArray($data);
                $result = $this->getSearchModel()->findAdvancedSearchResult($data['text'], $data['library']);
                if (count($result) == 0) {
                    $result = 'Result not found!';
                }

                $library = $data['library'];
            }
        }

        return array(
            'form' => $form,
            'result' => $result,
            'library' => $library,
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
