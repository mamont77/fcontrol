<?php
/**
 * @namespace
 */
namespace FcFlight\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Class FlightYearChecker
 * @package FcFlight\Validator
 */
class FlightYearChecker extends AbstractValidator
{
    const CURRENT_OR_ONE_MORE = 'CURRENT_OR_ONE_MORE';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::CURRENT_OR_ONE_MORE => "The year '%value%' must be a current or next"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $currentYear = date('Y');
        $lastYear = (int)$currentYear + 1;

        if (mb_strlen($value) == 10) {
            $value = \DateTime::createFromFormat('d-m-Y', $value);
        } else {
            $value = \DateTime::createFromFormat('d-m-Y H:i', $value);
        }
        $value = date('Y', $value->getTimestamp());
        $this->setValue($value);

        if ($value < $currentYear || $value > $lastYear) {
            $this->error(self::CURRENT_OR_ONE_MORE);
            return false;
        }

        return true;
    }
}
