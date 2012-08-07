<?php
/**
 * @author Antoine Hegdecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Sphinx\Hydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;

class QueryInfo implements HydratorInterface
{
    /**
     * @var array
     */
    protected $configuration = array(
        'filters'      => array(),
        'query_fields' => array()
    );

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = array_merge($this->configuration, $configuration);
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
        return $object['parameters'] = array(
            'values'        => $data,
            'configuration' => $this->configuration
        );
    }
}