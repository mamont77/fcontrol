<?php
/**
 * @namespace
 */
namespace FcFlight\Form;

use Zend\Form\Form;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Class BaseForm
 * @package FcFlight\Form
 */
class BaseForm extends Form
{
    /**
     * @param $libraryName
     * @param object|array $data
     * @param string $baseFieldKey
     * @param string $fieldName
     * @param string $type
     * @return $this
     */
    protected function setLibrary($libraryName, $data, $baseFieldKey = 'id', $fieldName = '', $type = 'object')
    {
        if (!$data) return $this;

        if (!$this->{$libraryName}) {
            if ($type == 'array') {
                foreach ($data as $row) {
                    if (is_array($fieldName)) {
                        if ($row[$fieldName[1]] != '') {
                            $fieldValue = $row[$fieldName[0]] . ' (' . $row[$fieldName[1]] . ')';
                        } else {
                            $fieldValue = $row[$fieldName[0]];
                        }
                        $this->{$libraryName}[$row[$baseFieldKey]] = $fieldValue;
                    } else {
                        if ($row[$fieldName] != '') {
                            $this->{$libraryName}[$row[$baseFieldKey]] = $row[$fieldName];
                        }
                    }
                }
            } else {
                foreach ($data as $row) {
                    if (is_array($fieldName)) {
                        if ($row->{$fieldName[1]} != '') {
                            $fieldValue = $row->{$fieldName[0]} . ' (' . $row->{$fieldName[1]} . ')';
                        } else {
                            $fieldValue = $row->{$fieldName[0]};
                        }
                        $this->{$libraryName}[$row->{$baseFieldKey}] = $fieldValue;
                    } else {
                        if ($row->{$fieldName} != '') {
                            $this->{$libraryName}[$row->{$baseFieldKey}] = $row->{$fieldName};
                        }
                    }
                }
            }
            uasort($this->{$libraryName}, array($this, 'sortLibrary'));
        }
        return $this;
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    protected function sortLibrary($a, $b)
    {
        return $a > $b;
    }
}
