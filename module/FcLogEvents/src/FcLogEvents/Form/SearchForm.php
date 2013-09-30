<?php

namespace FcLogEvents\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class SearchForm
 *
 * @package FcLogEvents\Form
 */
class SearchForm extends Form
{

    /**
     * @var array
     */
    protected $users;

    /**
     * @param null $name
     * @param array $params
     */
    public function __construct($name = null, $params = array())
    {
        parent::__construct($name);

        $this->setUsers($params['usersList']);

        $this->setName('logsSearch');
        $this->setAttributes(array(
            'method' => 'post',
            'action' => '/logs/search',
        ));

        $this->add(array(
            'name' => 'dateFrom',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Date from',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'name' => 'dateTo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Date to',
                'description' => 'DD-MM-YYYY',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'priority',
            'options' => array(
                'label' => 'Priority',
                'value_options' => array(
                    '' => 'Any',
                    '6' => 'Info',
                    '5' => 'Notice',
                    '4' => 'Warning',
                ),
            ),
            'attributes' => array(
                'value' => ''
            )
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'User',
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => true,
                'size' => 5,
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'User',
                'empty_option' => '-- Select --',
                'value_options' => $this->getUsers(),
            ),
        ));

        //Csrf
        $this->add(new Element\Csrf('csrf'));

        //Submit button
        $this->add(array(
            'name' => 'submitBtn',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Find',
            ),
        ));
    }

    /**
     * @param \Zend\Db\ResultSet\ResultSet $data
     */
    private function setUsers(\Zend\Db\ResultSet\ResultSet $data)
    {
        if (!$this->users) {
            foreach ($data as $row) {
                $this->users[$row->username] = $row->username;
            }
        }
    }

    /**
     * @return array
     */
    private function getUsers()
    {
        return $this->users;
    }
}
