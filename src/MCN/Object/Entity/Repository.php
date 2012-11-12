<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Object\Entity;

// MCN classes
use MCN\Object\QueryInfo,
    MCN\Object\Exception,
    MCN\Pagination\Pagination,
    MCN\Object\AbstractRepository;

// Doctrine classes
use Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr,
    Doctrine\ORM\QueryBuilder,
    Doctrine\ORM\NoResultException,
    Doctrine\ORM\Query\ResultSetMapping,
    Doctrine\ORM\NonUniqueResultException;

/**
 * @category MCN
 * @package Object
 * @subpackage Entity
 */
class Repository extends AbstractRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $manager;

    /**
     * @return string
     */
    protected function getRootEntityAlias()
    {
        return strtolower(substr($this->metadata->rootEntityName, strlen($this->metadata->namespace) + 1));
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInfo    $qi
     *
     * @return Repository
     * @throws \MCN\Object\Exception\BadMethodCallException
     * @throws \MCN\Object\Exception\InvalidArgumentException
     */
    private function addParametersToQuery(QueryBuilder $qb, QueryInfo $qi)
    {
        // Get the expression handler
        $expr = $qb->expr();

        foreach($qi->getParameters() as $param => $value)
        {
            if (! is_array($value)) {

                $value = $qb->expr()->literal($value);
            }

            // Parameters matches a field in the entity so just do a simple eq
            if (in_array($param, $this->metadata->fieldNames)) {

                $qb->add('where', $expr->eq($this->getRootEntityAlias() . '.' . $param, $value));
            } else {

                $exp = explode(':', $param);

                if (2 != count($exp)) {

                    throw new Exception\InvalidArgumentException(
                        sprintf('Invalid build parameters syntax for parameter %s', $param)
                    );
                }

                list($field, $method) = $exp;

                // The method not like does not exist so we create one
                if ($method == 'nlike') {

                    if (strstr($field, '.') !== false) {

                        $qb->andWhere($expr->not($expr->like($field, $value)));
                    } else {

                        $qb->andWhere($expr->not($expr->like($this->getRootEntityAlias() . '.' . $field, $value)));
                    }

                    continue;
                }

                if (! method_exists($expr, $method)) {

                    throw new Exception\BadMethodCallException(
                        sprintf('Invalid expression called, the method "%s" does not exist.', $method)
                    );
                }

                if (strstr($field, '.') !== false) {

                    $qb->andWhere($expr->$method($field, $value));
                } else {

                    $qb->andWhere($expr->$method($this->getRootEntityAlias() . '.' . $field, $value));
                }
            }
        }

        return $this;
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInfo    $qi
     *
     * @return Repository
     */
    private function addRelationsToQuery(QueryBuilder $qb, QueryInfo $qi)
    {
        foreach ($qi->getRelations() as $relation => $options)
        {
            if (! isSet($options['joinAlias'])) {

                $exp = explode('.', $relation);

                $joinAlias = end($exp);
            } else {

                $joinAlias = $options['joinAlias'];
            }

            // default parameters for join
            $joinType          = isSet($options['joinType']) ? $options['joinType'] : Expr\Join::LEFT_JOIN;
            $joinCondition     = isSet($options['joinCondition']) ? $options['joinCondition'] : null;
            $joinConditionType = isSet($options['joinContitionType']) ? $options['joinContitionType'] : null;
            $joinIndexBy       = isSet($options['indexBy']) ? $joinAlias . '.' . $options['indexBy'] : null;
            $joinFields        = isSet($options['fields']) ? $options['fields'] : null;

            if (strstr($relation, '.') === false) {

                $relation = $this->getRootEntityAlias() . '.' . $relation;
            }

            switch($joinType)
            {
                case Expr\Join::LEFT_JOIN:
                    $qb->leftJoin($relation, $joinAlias, $joinConditionType, $joinCondition, $joinIndexBy);
                    break;

                case Expr\Join::INNER_JOIN:
                    $qb->innerJoin($relation, $joinAlias, $joinConditionType, $joinCondition, $joinIndexBy);
                    break;
            }

            if (null === $joinFields || empty($joinFields)) {

                $qb->addSelect($joinAlias);
            } else {

                $qb->addSelect('partial friends.{' . implode(',', $joinFields) . '}');
            }
        }

        return $this;
    }

    private function addSortToQuery(QueryBuilder $qb, QueryInfo $qi)
    {
        $sort = $qi->getSort();

        foreach($sort as $field => $direction)
        {
            if ($this->metadata->hasField($field)) {

                $qb->addOrderBy($this->getRootEntityAlias() . '.' . $field, $direction);

                unset($sort[$field]);
            }
        }

        $qi->setSort($sort);
    }

    /**
     * @param \MCN\Object\QueryInfo $qi
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQuery(QueryInfo $qi)
    {
        $qb    = $this->manager->createQueryBuilder();
        $alias = $this->getRootEntityAlias();

        // A numerical default value would cause unexpected results
        if (null !== $qi->getLimit()) {

            $qb->setMaxResults($qi->getLimit());
        }

        if (null !== $qi->getOffset()) {

            $qb->setFirstResult($qi->getOffset());
        }

        // Setup from the from
        if (null !== $qi->getIndexBy()) {

            $qb->add(
                    'from',
                    $this->metadata->name. ' ' . $alias .
                    ' INDEX BY ' . $alias . '.' . $qi->getIndexBy()
                );
        } else {

            $qb->from($this->metadata->name, $alias);
        }

        // Check if we want all fields or just some specific
        $fields = $qi->getFields();

        if (! empty($fields)) {

            $qb->add('select', 'partial ' . $alias. '.{' . implode(',', $fields) . '}', true);
        } else {

            $qb->select($alias);
        }

        $this->addRelationsToQuery($qb, $qi)
             ->addParametersToQuery($qb, $qi)
             ->addSortToQuery($qb, $qi);

        return $qb;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param \MCN\Object\QueryInfo     $q
     *
     * @return \Doctrine\ORM\Query
     */
    protected function getQuery(QueryBuilder $qb, QueryInfo $q)
    {
        $query = $qb->getQuery();

        if ($q->useCache()) {

            $options = $q->getCache();

            $query->useResultCache(true, $options['ttl'], $options['name']);
        }

        return $query;
    }

    /**
     * Retrieve a single object using the specified query information
     *
     * @throws \MCN\Object\Exception\InvalidArgumentException
     * @param mixed $qi
     *
     * @return mixed
     */
    public function fetchOne($qi)
    {
        if (is_array($qi)) {

            $qi = new QueryInfo($qi);

        } else {
            if (!$qi instanceof QueryInfo) {

                throw new Exception\InvalidArgumentException(
                    sprintf('%s required the first argument be an array or an instance of QueryInfo', __METHOD__)
                );
            }
        }

        $query = $this->getBaseQuery($qi);
        $query = $this->getQuery($query, $qi);

        try {

            return $query->getSingleResult();
        } catch(NonUniqueResultException $e) {
        } catch(NoResultException $e) {
        }

        return null;
    }

    /**
     * Retrieve a single object using the specified query information
     *
     * @throws \MCN\Object\Exception\InvalidArgumentException
     * @param mixed $qi
     *
     * @return Pagination|array
     */
    public function fetchAll($qi)
    {
        if (is_array($qi)) {

            $qi = new QueryInfo($qi);

        } else {
            if (!$qi instanceof QueryInfo) {

                throw new Exception\InvalidArgumentException(
                    sprintf('%s required the first argument be an array or an instance of QueryInfo', __METHOD__)
                );
            }
        }

        $query = $this->getBaseQuery($qi);
        $query = $this->getQuery($query, $qi);

        $result = $query->getResult($qi->getHydration());

        // Query info specified to count available rows
        if ($qi->getCountAvailableRows()) {

            $countQuery = clone $query;
            $countQuery->setParameters($query->getParameters());

            foreach ($query->getHints() as $name => $value)
            {
                $countQuery->setHint($name, $value);
            }

            $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

            $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\CountOutputWalker');
            $countQuery->setResultSetMapping($rsm);
            $countQuery->setFirstResult(null)
                       ->setMaxResults(null);

            try {
                $data  =  $countQuery->getScalarResult();
                $data  = array_map('current', $data);
                $count = array_sum($data);

            } catch(NoResultException $e) {

                $count = 0;
            }

            return Pagination::create($result, $count, $qi);

        } else {

            return $result;
        }
    }
}
