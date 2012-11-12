<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN\Stdlib;

/**
 * @category MCN
 * @package Stdlib
 */
class NamingConvention
{
    /**
     * Convert a underscore spaced string to camel case
     *
     * @static
     * @param string $name
     * @return string
     */
    static public function toCamelCase($name)
    {
        return implode('', array_map('ucfirst', explode('_', $name)));
    }

    /**
     * Converts a camel cased string to underscore separated
     *
     * @static
     * @param string $name
     * @return string
     */
    static public function fromCamelCase($name)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
    }
}
