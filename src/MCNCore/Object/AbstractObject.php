<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Object;
use ArrayAccess;

/**
 * @category MCNCore
 * @package Object
 */
abstract class AbstractObject implements ArrayAccess
{
    /**
     * @static
     * @param $field
     * @return string
     */
    static public function toCamelCase($field)
    {
        return implode('', array_map('ucfirst', explode('_', $field)));
    }

    /**
     * @static
     * @param $field
     * @return string
     */
    static public function fieldToGetterMethod($field)
    {
        return 'get' . static::toCamelCase($field);
    }

    /**
     * @static
     * @param $field
     * @return string
     */
    static public function fieldToSetterMethod($field)
    {
        return 'set' . static::toCamelCase($field);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return method_exists($this, static::fieldToGetterMethod($offset));
    }

    /**
     * @throws \MCNCore\Object\Exception\LogicException
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $method = static::fieldToGetterMethod($offset);

        if (method_exists($this, $method)) {

            return $this->{$method}();
        } else {

            throw new Exception\LogicException(
                sprintf('Missing getter method for field "%s" on object class', $offset, get_called_class())
            );
        }
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws Exception\LogicException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $method = static::fieldToSetterMethod($offset);

        if (method_exists($this, $method)) {

            $this->{$method}($value);
        } else {

            throw new Exception\LogicException(
                sprintf('Missing setter method for field "%s" on object class %s', $offset, get_called_class())
            );
        }
    }

    /**
     * Unsets a field, doing this will actually set it to the default property value
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        if($this->offsetExists($offset))
        {
            $reflection = new ReflectionClass(get_called_class());
            $properties = $reflection->getDefaultProperties();

            $this->offsetSet($offset, $properties[$offset]);
        }
    }

    /**
     * Applies all the properties to the current object if a setter exist for them.
     * Silently ignores properties that don't exist.
     *
     * @param array $properties
     * @return void
     */
    public function fromArray(array $properties)
    {
        foreach($properties as $property => $value)
        {
            if ($this->offsetExists($property)) {

                $this->offsetSet($property, $value);
            }
        }
    }

    /**
     * Converts the current object into an array with the possibility of recursion
     *
     * @param bool $recursive
     *
     * @return array
     */
    public function toArray($recursive = false)
    {
        $vars = get_object_vars($this);

        foreach($vars as $key => &$var) {

            if ($recursive && $var instanceof AbstractObject) {

                if (isSet($var->__isInitialized__)) {

                    if ($var->__isInitialized__) {

                        $var = $var->toArray(true);
                    } else {

                        unset($vars[$key]);
                    }
                }
            }
        }

        return $vars;
    }
}