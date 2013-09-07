<?php

namespace FcFlight\Validator;

use Zend\Validator\AbstractValidator;

class dateOfFlight extends AbstractValidator
{
    const GREATER_OR_EQUAL = 'GoE';

    protected $messageTemplates = array(
        self::GREATER_OR_EQUAL => "The data '%value%' most be greater or equal than the previous Date Of Flight"
    );

    public function isValid($value, $options = array())
    {

        $this->setValue($value);

        $dateOfFlight = \DateTime::createFromFormat('d-m-Y', $value);
        $previousDateOfFlight = \DateTime::createFromFormat('d/m/Y', $options['previousDateOfFlight']);

        if ($dateOfFlight < $previousDateOfFlight) {
            $this->error(self::GREATER_OR_EQUAL);
            return false;
        }

        return true;
    }
}
