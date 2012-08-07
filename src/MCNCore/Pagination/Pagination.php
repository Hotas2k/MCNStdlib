<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Pagination;
use Countable,
    IteratorAggregate,
    MCNCore\Object\QueryInfo;

class Pagination implements Countable, IteratorAggregate
{
    /**
     * @var mixed
     */
    protected $collection = array();

    /**
     * @var array
     */
    protected $options = array(
        'itemsPerPage' => 20,
        'totalCount' => 0,
        'currentPage' => 1,
        'pageRange' => 5
    );

    /**
     * Creates a new instance
     *
     * @param mixed $collection
     * @param array $options
     *
     * @return \Pagination
     */
    public function __construct($collection, array $options = array())
    {
        $this->collection = $collection;

        if (! empty($options)) {

            foreach($options as $option => $value)
            {
                $method = 'set' . ucfirst($option);

                if(method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * Creates a pagination from the default information generated
     *
     * @param array     $resultset
     * @param integer   $num_rows
     * @param QueryInfo $qi
     *
     * @return \Pagination
     */
    static public function create(array $resultset, $num_rows, QueryInfo $qi)
    {
        $pagination = new static($resultset);
        $pagination->setTotalCount($num_rows)
                   ->setItemsPerPage($qi->getLimit())
                   ->setCurrentPage($qi->getOffset() != null ? ($qi->getOffset() / $qi->getLimit()) + 1 : 1);

        return $pagination;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->collection;
    }

    /**
     * @param Integer $count
     * @return Pagination fluent interface
     */
    public function setItemsPerPage($count)
    {
        $this->options['itemsPerPage'] = $count;

        return $this;
    }

    /**
     * @param Integer $count
     * @return Pagination fluent interface
     */
    public function setTotalCount($count)
    {
        $this->options['totalCount'] = $count;

        return $this;
    }

    /**
     * @param Integer $page
     * @return Pagination fluent interface
     */
    public function setCurrentPage($page)
    {
        $this->options['currentPage'] = $page;

        return $this;
    }

    /**
     * @param Integer $range
     * @throws InvalidArgumentException on invalid range
     * @return Pagination
     */
    public function setPageRange($range)
    {
        if (1 != $range % 2) {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts an odd numeric value as argument.');
        }

        $this->options['pageRange'] = $range;
    }

    /**
     * Returns the current page
     *
     * @return Integer
     */
    public function getCurrentPage()
    {
        return $this->options['currentPage'];
    }

    /**
     * Get the last page
     *
     * @return Integer last page
     */
    public function getLastPage()
    {
        return ceil($this->options['totalCount'] / $this->options['itemsPerPage']);
    }

    /**
     * getFirstIndicee
     *
     * Return the first indice number for the current page
     *
     * @return int First indice number
     */
    public function getFirstIndice()
    {
        return ($this->getCurrentPage() - 1) * $this->options['itemsPerPage'] + 1;
    }

    /**
     * getLastIndice
     *
     * Return the last indice number for the current page
     *
     * @return int Last indice number
     */
    public function getLastIndice()
    {
        return min($this->getTotalCount(), ($this->getCurrentPage() * $this->options['itemsPerPage']));
    }


    /**
     * @return Integer total number of rows existing
     */
    public function getTotalCount()
    {
        return $this->options['totalCount'];
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return ($this->options['totalCount'] != 0 && $this->options['totalCount'] > $this->options['itemsPerPage']);
    }

    /**
     * @return Array
     */
    public function getRange()
    {
        $page  = $this->getCurrentPage();
        $pages = $this->getLastPage();

        $chunk = $this->options['pageRange'];

        if ($chunk > $pages) {
            $chunk = $pages;
        }

        $chunkStart = $page - (floor($chunk / 2));
        $chunkEnd   = $page + (ceil($chunk / 2)-1);

        if ($chunkStart < 1) {
            $adjust = 1 - $chunkStart;
            $chunkStart = 1;
            $chunkEnd = $chunkEnd + $adjust;
        }

        if ($chunkEnd > $pages) {
            $adjust = $chunkEnd - $pages;
            $chunkStart = $chunkStart - $adjust;
            $chunkEnd = $pages;
        }

        return range($chunkStart, $chunkEnd);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if(is_array($this->collection)) {

            return new \ArrayIterator($this->collection);
        }

        return $this->collection;
    }
}