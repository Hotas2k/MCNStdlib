<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Form\Hydrator;
use DateTime,
    Zend\Stdlib\Hydrator\HydratorInterface;

class DateFieldset implements HydratorInterface
{
    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        // We cannot do shit here
        if (! $object instanceof DateTime) {

            return array();
        }

        return array(
            'year'  => $object->format('Y'),
            'month' => $object->format('m'),
            'day'   => $object->format('d')
        );
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' 00:00:00');
    }
}