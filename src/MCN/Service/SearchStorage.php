<?php
/**
 * @author Antoine Hedgecock
 */

/**
 * @namespace
 */
namespace MCN\Service;
use Doctrine\ORM\EntityManager,
    MCN\Entity\SearchStorage as SearchStorageEntity;

class SearchStorage
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \MCNCore\Object\Entity\Repository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('MCN\Entity\SearchStorage');
    }

    /**
     * Check the repository if a search with those parameters already has been done
     *
     * @param string $hash
     * @param string $namespace
     *
     * @return mixed
     */
    public function has($hash, $namespace)
    {
        $qi = array(
            'parameters' => array(
                'hash:eq'      => $hash,
                'namespace:eq' => $namespace
            )
        );

        return $this->getRepository()
                    ->fetchOne($qi);
    }

    /**
     * Inserts a new record into the repository
     *
     * @param array  $parameters
     * @param string $hash
     * @param string $namespace
     *
     * @return \MCN\Entity\SearchStorage
     */
    public function insert(array $parameters, $hash, $namespace)
    {
        $entity = new SearchStorageEntity();
        $entity->fromArray(
            array(
                'hash'       => $hash,
                'namespace'  => $namespace,
                'parameters' => $parameters
            )
        );

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * Retrieves the search parameters from the database
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function get($id)
    {
        $qi = array(
            'parameters' => array(
                'id:eq'  => $id
            )
        );

        return $this->getRepository()
                    ->fetchOne($qi);
    }
}