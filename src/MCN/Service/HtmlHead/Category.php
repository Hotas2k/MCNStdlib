<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Service\HtmlHead;
use Doctrine\ORM\EntityManager;

use Zend\Log\LoggerInterface,
    Zend\Log\LoggerAwareInterface;

use MCN\Entity\HtmlHead\Category as CategoryEntity;

/**
 *
 */
class Category implements LoggerAwareInterface
{
    const SORT_NAME  = 'name';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Doctrine\ORM\EntityManager $em
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
        return $this->em->getRepository('MCN\Entity\HtmlHead\Category');
    }

    /**
     * @param \Zend\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $name
     *
     * @return CategoryEntity|null
     */
    public function get($name)
    {
        $options = array(
            'parameters' => array(
                'name:eq' => $name
            )
        );

        return $this->getRepository()
                    ->fetchOne($options);
    }

    /**
     * @param $id
     *
     * @return CategoryEntity|null
     */
    public function getById($id)
    {
        $options = array(
            'parameters' => array(
                'id:eq' => $id
            )
        );

        return $this->getRepository()
                    ->fetchOne($options);
    }

    /**
     * @param string $name
     *
     * @return CategoryEntity
     */
    public function create($name)
    {
        $entity = new CategoryEntity();
        $entity->setName($name);

        $this->save($entity);

        // Notify administrators a new category has been created.
        $this->logger->alert(sprintf('Htmlhead (SEO): a new category has been created named "%s"', $name));

        return $entity;
    }

    public function save(CategoryEntity $entity)
    {
        if (! $this->em->getUnitOfWork()->isInIdentityMap($entity)) {

            $this->em->persist($entity);
        }

        $this->em->flush();
    }

    /**
     * @param array $options
     *
     * @return array|\MCNCore\Pagination\Pagination
     */
    public function fetchAll(array $options = array())
    {
        return $this->getRepository()
                    ->fetchAll($options);
    }
}