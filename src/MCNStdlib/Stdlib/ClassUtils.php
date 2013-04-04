<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNStdlib\Stdlib;

/**
 * Class ClassUtils
 * @package MCNStdlib\Stdlib
 */
class ClassUtils
{
    /**
     *
     */
    final private function __construct()
    {}

    /**
     * Check if a class uses a specific trait
     *
     * @static
     *
     * @param string $class
     * @param string $trait
     *
     * @throws Exception\InvalidArgumentException
     * @return bool
     */
    public static function uses($class, $trait)
    {
        if (! is_object($class)) {

            throw new Exception\InvalidArgumentException('First argument is expected to an object');
        }

        $classes = array($class) + class_parents($class);

        foreach($classes as $class) {

            if (in_array($trait, class_uses($class))) {

                return true;
            }
        }

        return false;
    }
}
