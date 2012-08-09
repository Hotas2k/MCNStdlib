<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Sphinx;
use SphinxClient,
    MCNCore\Object\QueryInfo,
    MCNCore\Pagination\Pagination;

/**
 * @category
 */
trait SearchTrait
{
    /**
     * @var SphinxClient
     */
    protected $client;

    /**
     * @param Sphinx $sphinx
     */
    public function setSphinxClient(SphinxClient $sphinx)
    {
        $this->client = $sphinx;

        return $this;
    }

    /**
     * Takes the query info object and executes the query against the sphinx server
     *
     * @todo: there is some duplication of code that could be removed (return the object in the correct format etc..)
     *
     * @param string $index
     * @param mixed  $qi
     * @param bool   $getRawData
     *
     * @return array|\Pagination
     */
    protected function doSphinxSearch($index, $qi, $getRawData = false)
    {
        // convert an array to a query info
        if (is_array($qi) || $qi instanceof \Traversable) {

            $qi = new QueryInfo($qi);
        }

        // Apply limit and offset (not sure why you need to specify max matches)
        $this->client->setLimits($qi->getOffset(), $qi->getLimit(), 1000);

        // All search results should sort by relevance
        $this->client->setSortMode(SPH_SORT_EXTENDED, $this->getSphinxSortString($qi->getSort()));

        $parameters  = $qi->getParameters();
        $queryString = '';

        if (! empty($parameters)) {

            // process the parameters
            $queryString = $this->processParameters($parameters);
        }

        // execute query
        $result = $this->client->query($queryString, $index);

        if (! $result) {

            throw new Exception\RuntimeException($this->client->getLastError());
        }

        // No matches found
        if (! isSet($result['matches'])) {

            // return an array instead of a pagination object
            if ($getRawData) {

                return array(
                    'row_set'  => array(),
                    'num_rows' => 0
                );
            }

            // empty pagination object
            return Pagination::create(array(), 0, $qi);
        }

        // return the raw data
        if ($getRawData) {

            return array(
                'num_rows' => $result['total_found'],
                'row_set'  => $result['matches']
            );
        }

        // clone the qi
        $repoQi = clone $qi;
        $repoQi->setOptions(
            array(
                 'offset'  => 0,
                 'indexBy' => 'id',

                 'parameters' => array(
                     'id:in'  => array_keys($result['matches'])
                 )
            )
        );

        // get the doctrine objects & relations if any specified
        $entities = $this->getRepository()
                         ->fetchAll($repoQi);

        $sorted = array();

        // since rbdms don't return the object in the same way as they are listed in the sql "in" clause
        // we need to sort them according to the
        foreach(array_keys($result['matches']) as $id)
        {
            // Skip if the are not found, this can happen (perhaps we should log)
            // todo: add log when this happens to see how often it happens
            if (! isSet($entities[$id])) continue;

            $sorted[] = $entities[$id];
        }

        return Pagination::create($sorted, $result['total_found'], $qi);
    }

    /**
     * Starts out by sorting by relevance and then follow what ever was passed in the first parameter
     *
     * @todo: should probably check if the field exists
     *
     * @param array $sort
     *
     * @return string
     */
    private function getSphinxSortString(array $sort)
    {
        $return = array(
            '@relevance DESC'
        );

        foreach($sort as $field => $direction)
        {
            $return[] = $field . ' ' . $direction;
        }

        return implode(', ', $return);
    }

    /**
     * Messy but works, processes the parameters and applies them to the sphinx client
     *
     * @todo: rewrite this so that is follows the "standard" DQB/QueryInfo E.g using field:match => 'value' and field:setRange => array(100, 110)
     *
     * @throws Exception\LogicException
     *
     * @param array  $parameters
     * @return string
     */
    private function processParameters(array $parameters)
    {
        // Apply the match mode else the parameters are going to flip out
        $this->client->setMatchMode(SPH_MATCH_EXTENDED);

        // Process query fields, uses an external method to allow for recursion
        $queries = $this->processQueryFields($parameters['configuration']['query_fields'], $parameters['values']);

        // Apply the current query filters
        $this->processQueryFilters($parameters['configuration']['filters'], $parameters['values']);

        // Apply
        return implode(' ', $queries);
    }

    /**
     * @throws Exception\InvalidArgumentException
     *
     * @param array $filters
     * @param array $values
     *
     * @return void
     */
    private function processQueryFilters(array $filters, $values)
    {
        foreach($filters as $index => $filter)
        {
            if (is_array($filter)) {

                $this->processQueryFilters($filter, $values[$index]);

                continue;
            }

            if (! is_callable($filter)) {

                throw new Exception\InvalidArgumentException(
                    sprintf('Invalid filter specified must be a valid callable object')
                );
            }

            // Incase we need to pass multiple parameters to the filter
            if (strpos($index, ',') !== false) {

                $parameters = array($this->client);

                foreach(explode(',', $index) as $param)
                {
                    $parameters[] = $values[$param];
                }

                call_user_func_array($filter, $parameters);

            } else {

                call_user_func_array($filter, array($this->client, $values[$index]));
            }
        }
    }

    /**
     * @param array $query_fields
     *
     * @return array
     */
    private function processQueryFields(array $query_fields, $values)
    {
        $queries = array();

        foreach($query_fields as $index => $fields)
        {
            if (is_array($fields)) {

                $queries += $this->processQueryFields($query_fields[$index], $values[$index]);

                continue;
            }

            $field = isSet($values[$index]) ? $values[$index] : null;

            if (!empty($field)) {

                $fields = explode(',', $fields);

                $pattern = count($fields) > 1 ? '@(%s) %s' : '@%s %s';

                // convert a bunch of weird stuff to a real query
                $query = sprintf(
                // The pattern
                    $pattern,

                    // the fields
                    implode(',', $fields),

                    // The data
                    $this->client->escapeString($field)
                );

                $queries[] = $query;
            }
        }

        return $queries;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract protected function getRepository();
}