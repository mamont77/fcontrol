<?php

namespace FcFlight\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Class FlightDateChecker
 * @package FcFlight\Validator
 */
class FlightDateChecker extends AbstractValidator
{
    const GREATER_OR_EQUAL = 'GoE';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::GREATER_OR_EQUAL => "The current date '%value%' must be greater than or equal to the previous date"
    );

    /**
     * @param mixed $value
     * @param array $options
     * @return bool
     */
    public function isValid($value, $options = array())
    {

        $this->setValue($value);

        $currentDate = \DateTime::createFromFormat('d-m-Y', $value);
        $previousDate = \DateTime::createFromFormat('d-m-Y', $options['previousDate']);

        if ($currentDate < $previousDate) {
            $this->error(self::GREATER_OR_EQUAL);
            return false;
        }

        return true;
    }
}
