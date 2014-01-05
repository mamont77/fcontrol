<?php
/**
 * @namespace
 */
namespace FcFlightManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class NumberToWords
 * @package FcFlightManagement\View\Helper
 */
class NumberToWords extends AbstractHelper
{

    public function __invoke($number)
    {
        $number = ( string )(( int )$number);

        if (( int )($number) && ctype_digit($number)) {
            $words = array();

            $number = str_replace(array(',', ' '), '', trim($number));

            $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
                'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen',
                'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');

            $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty',
                'seventy', 'eighty', 'ninety', 'hundred');

            $list3 = array('', 'thousand', 'million', 'billion', 'trillion',
                'quadrillion', 'quintillion', 'sextillion', 'septillion',
                'octillion', 'nonillion', 'decillion', 'undecillion',
                'duodecillion', 'tredecillion', 'quattuordecillion',
                'quindecillion', 'sexdecillion', 'septendecillion',
                'octodecillion', 'novemdecillion', 'vigintillion');

            $number_length = strlen($number);
            $levels = ( int )(($number_length + 2) / 3);
            $max_length = $levels * 3;
            $number = substr('00' . $number, -$max_length);
            $number_levels = str_split($number, 3);

            foreach ($number_levels as $number_part) {
                $levels--;
                $hundreds = ( int )($number_part / 100);
                $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
                $tens = ( int )($number_part % 100);
                $singles = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = ( int )($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = ( int )($number_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && ( int )($number_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }

            $commas = count($words);

            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words = implode(', ', $words);

            //Some Finishing Touch
            //Replacing multiples of spaces with one space
            $words = trim(str_replace(' ,', ',', $this->trim_all(ucwords($words))), ', ');
            if ($commas) {
                $words = $this->str_replace_last(',', ' and', $words);
            }

            return $words;
        } else if (!(( int )$number)) {
            return 'Zero';
        }
        return '';
    }

    public function trim_all($str, $what = NULL, $with = ' ')
    {
        if ($what === NULL) {
            //  Character      Decimal      Use
            //  "\0"            0           Null Character
            //  "\t"            9           Tab
            //  "\n"           10           New line
            //  "\x0B"         11           Vertical Tab
            //  "\r"           13           New Line in Mac
            //  " "            32           Space

            $what = "\\x00-\\x20"; //all white-spaces and control chars
        }

        return trim(preg_replace("/[" . $what . "]+/", $with, $str), $what);
    }

    public function str_replace_last($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
}
