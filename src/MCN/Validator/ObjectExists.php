<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Validator;
use Zend\Validator\AbstractValidator;

use Doctrine\Common\Persistence\ObjectManager;

/**
 *
 */
class ObjectExists extends AbstractValidator
{
    /**
     *
     */
    const ENTITY_CLASS_NOT_FOUND = 'entity_class_not_found';

    /**
     *
     */
    const ENTITY_EXISTS = 'entity_exists';

    protected $messageTemplates = array(
        self::ENTITY_EXISTS          => 'An object was found',
        self::ENTITY_CLASS_NOT_FOUND => 'Could find the specified entity class'
    );

    protected $messageVariables = array(
        'value' => 'value'
    );


    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var bool
     */
    private $isCloned = false;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $object;

    public function setObjectManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     *
     */
    public function __clone()
    {
        $this->isCloned = true;
    }

    /**
     * @throws Exception\RuntimeException
     */
    private function checkIfCloned()
    {
        if (!$this->isCloned) {

            throw new Exception\RuntimeException('Options cannot be passed before cloning this object.');
        }
    }

    /**
     * @throws Exception\InvalidArgumentException
     *
     * @param mixed $object
     *
     * @return \MCN\Validator\ObjectExists
     */
    public function setObject($object)
    {
        if (is_object($object)) {

            $object = get_class($object);

        }

        if (! is_string($object)) {

            throw new Exception\InvalidArgumentException('Only an object or string with a FQCN is allowed.');
        }

        $this->object = $object;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $property
     *
     * @return ObjectExists
     */
    public function setField($property)
    {
        $this->checkIfCloned();

        $this->field = $property;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     *
     * @return boolean
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $this->checkIfCloned();

        $result = $this->manager->getRepository($this->object, $value);

        $options = array(
            'parameters' => array(
                $this->getField() . ':eq'  => $value
            )
        );

        if ($result->fetchOne($options)) {

            $this->error(self::ENTITY_EXISTS, $value);

            return false;
        }

        return true;
    }
}
