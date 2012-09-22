<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\View\Helper;
use Zend\View\Helper\AbstractHelper;

class StringTrim extends AbstractHelper
{
    public function __invoke($string, $length = 25, $delimiter = '...')
    {
        $len = strlen($string);

        if ($len <= $length) {
            return $string;
        }

        $shortened = substr($string, 0, $length);

        $pos = strrpos($shortened, ' ');

        if ($pos !== false) {
            return substr($shortened, 0, $pos) . $delimiter;
        } else {
            return $shortened . $delimiter;
        }
    }
}
