<?php
/**
 * @namespace
 */
namespace FcFlight\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FcFlight\Form\SearchForm;

/**
 * Class SearchController
 *
 * @package FcFlight\Controller
 */
class SearchController extends FlightController
{
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
            if ($data->dateOrderFrom == ''
                && $data->dateOrderTo == ''
                && $data->customer == ''
                && $data->airOperator == ''
                && $data->aircraft == ''
            ) {
                $result = 'Result not found. Enter one or more fields.';
            } else {
                $filter = $this->getServiceLocator()->get('FcFlight\Filter\SearchFilter');
                $searchForm->setInputFilter($filter->getInputFilter());
                $searchForm->setData($request->getPost());
                if ($searchForm->isValid()) {
                    $data = $searchForm->getData();
                    $filter->exchangeArray($data);
                    $result = $this->getSearchModel()->findSearchResult($filter);
                    if (count($result) == 0) {
                        $data = 'Result not found!';
                    } else {
                        $data = array();
                        foreach ($result as $key => $row) {
                            foreach ($this->_mapFields as $field) {
                                if (isset($row->$field)) {
                                    $data[$key][$field] = $row->$field;
                                }
                            }
                            try {
                                $hasRefuel = $this->getRefuelModel()->getByHeaderId($data[$key]['id']);
                                if (!empty($hasRefuel)) {
                                    $data[$key]['refuelStatus'] = 'YES';
                                } else {
                                    $data[$key]['refuelStatus'] = 'NO';
                                }
                            } catch (Exception $e) {
                                // do nothing
                            }

                            // TODO: Fix me after Permission feature.
                            $data[$key]['permitStatus'] = 'NO';
                        }
                    }
                }
            }
        }

        return new ViewModel(array(
            'data' => $data,
            'searchForm' => $searchForm,
            'route' => 'flightsSearch',
        ));
    }
}
