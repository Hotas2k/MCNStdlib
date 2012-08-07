<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Object;

/**
 * Object passed that should be passed to a object repository allowing a simple abstract of query information
 *
 * @category PMG
 * @package Entity
 * @subpackage Repository
 */
class QueryInfo
{
    /**
     * Sorts ascending
     */
    const SORT_ASC = 'ASC';

    /**
     * Sorts descending
     */
    const SORT_DESC = 'DESC';

    /**
     * Hydrates an object graph. This is the default behavior.
     */
    const HYDRATE_OBJECT = 1;

    /**
     * Hydrates an array graph.
     */
    const HYDRATE_ARRAY = 2;

    /**
     * Hydrates a flat, rectangular result set with scalar values.
     */
    const HYDRATE_SCALAR = 3;

    /**
     * Hydrates a single scalar value.
     */
    const HYDRATE_SINGLE_SCALAR = 4;

    /**
     * Which hydration mode that should be used
     *
     * @var int
     */
    protected $hydration = self::HYDRATE_OBJECT;

    /**
     * Relations that should be queried for
     *
     * @var array
     */
    protected $relations = array();

    /**
     * How to sort the query
     *
     * @var array
     */
    protected $sort = array();

    /**
     * Options used to build the query
     *
     * @var array
     */
    protected $options = array();

    /**
     * Parameters to use when querying
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * If the result should be cached
     *
     * @var null|array
     */
    protected $cache = null;

    /**
     * The fields that the query should retrive
     *
     * @var array
     */
    protected $fields = array();

    /**
     * If the we should query for the number of available rows
     *
     * @var bool
     */
    protected $countAvailableRows = false;

    /**
     * Default options for cache
     *
     * @var array
     */
    protected $defaultCache = array(
        'ttl'   => 3600,
        'name'  => null
    );

    /**
     * @var null|integer
     */
    protected $offset = null;

    /**
     * @var null|integer
     */
    protected $limit = null;

    /**
     * @var null|integer
     */
    protected $indexBy = null;

    /**
     * @param Zend_Config|array $options
     * @return void
     */
    public function __construct($options = array())
    {
        // Allow support for zend_config
        if ($options instanceof \Zend_Config) {

            $options = $options->toArray();
        }

        $this->setOptions($options);
    }

    /**
     * @param array $options
     * @return QueryRequirements fluent interface
     */
    public function setOptions(Array $options)
    {
        foreach($options as $option => $value) {

            $method = 'set' . ucfirst($option);

            if (method_exists($this, $method)) {

                $this->$method($value);
            } else {

                throw new \InvalidArgumentException(
                    sprintf(
                        'Unknown method %s being called on object %s',
                        $method,
                        __CLASS__
                    )
                );
            }
        }

        return $this;
    }

    /**
     * @param $bool
     *
     * @return QueryInfo
     */
    public function setCountAvailableRows($bool)
    {
        $this->countAvailableRows = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCountAvailableRows()
    {
        return $this->countAvailableRows;
    }

    /**
     * @param int $mode
     * @return QueryRequirements
     */
    public function setHydration($mode)
    {
        $this->hydration = $mode;

        return $this;
    }

    /**
     * Returns the mode in which the result should be hydrated
     *
     * @return int
     */
    public function getHydration()
    {
        return $this->hydration;
    }

    /**
     * Set/Append options used to build the query
     *
     * @param array $options
     * @param bool  $append
     * @return QueryRequirements fluent interface
     */
    public function setQueryOptions(array $options, $append = false)
    {
        if (! $append) {

            $this->options = $options;
        } else {

            $this->options = array_merge($this->options, $options);
        }

        return $this;
    }

    /**
     * Return the options used to build the query
     *
     * @return array
     */
    public function getQueryOptions()
    {
        return $this->options;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param      $name
     * @param      $value
     * @param bool $overwrite
     *
     * @return QueryInfo
     * @throws Exception\InvalidArgumentException
     */
    public function addParameter($name, $value, $overwrite = false)
    {
        if (! $overwrite && array_key_exists($name, $this->parameters)) {

            throw new Exception\InvalidArgumentException(
                sprintf('Method %s was called given the param %s which already exists but overwrite was false', __METHOD__, $name)
            );
        }

        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Clears the current set of relations and adds the new ones
     *
     * @param array $relations
     * @return QueryRequirements fluent interface
     */
    public function setRelations(array $relations)
    {
        $this->relations = array();

        foreach($relations as $relation => $options) {

            $this->addRelation($relation, $options);
        }

        return $this;
    }

    /**
     * Appends a relation
     *
     * @param string $relation
     * @param mixed $options
     * @return QueryRequirements fluent interface
     */
    public function addRelation($relation, $options)
    {
        if (! is_array($options)) {

            if (is_numeric($relation) && is_string($options)) {
                $relation = $options;
                $options  = array();

            } else if (is_string($relation) && is_string($options)) {
                $options = array(
                    'alias' => $options
                );
            }
        }

        if (! array_key_exists($relation, $this->relations)) {

            $this->relations[$relation] = $options;
        }

        return $this;
    }

    /**
     * Returns an array of relations
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Clears the current sorting and adds the new
     *
     * @param array $sort
     * @return QueryRequirements
     */
    public function setSort(Array $sort)
    {
        $this->sort = array();

        foreach($sort as $field => $order) {

            $this->addSort($field, $order);
        }

        return $this;
    }

    /**
     * Append a sorting method
     *
     * @param string $field
     * @param string $order
     * @return QueryRequirements fluent interface
     */
    public function addSort($field, $order = self::SORT_ASC)
    {
        if (! array_key_exists($field, $this->sort)) {

            $this->sort[$field] = $order;
        }
    }

    /**
     * Returns an array on how the result should be sorted
     *
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set the options for the cache
     *
     * @param array $options
     * @return QueryRequirements fluent interface
     */
    public function setCache(array $options)
    {
        $this->cache = array_merge($this->defaultCache, $options);

        return $this;
    }

    /**
     * Get the cache options
     *
     * @return bool
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns a bool value on if the result should be cached
     *
     * @return bool
     */
    public function useCache()
    {
        return null !== $this->cache;
    }

    /**
     * Set the number of results to be retrieved
     *
     * @param int $limit
     * @return QueryRequirements fluent interface
     */
    public function setLimit($limit)
    {
        if ($limit <= 0) {

            throw new Exception\InvalidArgumentException(__METHOD__ . ' requires that the first argument is a integer greater then 0');
        }

        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * Get the number of results to be retrieved
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the offset of the results to be retrieved
     *
     * @param int $offset
     * @return QueryRequirements fluent interface
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Get the offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getIndexBy()
    {
        return $this->indexBy;
    }

    /**
     * @param string $field
     * @return void
     */
    public function setIndexBy($field)
    {
        $this->indexBy = $field;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return QueryRequirements
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}