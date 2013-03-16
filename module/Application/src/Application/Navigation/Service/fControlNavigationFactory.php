<?php
namespace Application\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;

class fControlNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @abstract
     * @return string
     */
    protected function getName() {
        return 'fcontrol';
    }
}
