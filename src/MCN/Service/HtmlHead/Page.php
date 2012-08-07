<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */

namespace MCN\Service\HtmlHead;
use MCNCore\Object\AbstractObject,
    MCN\Entity\HtmlHead\Page      as PageEntity,
    MCN\Service\HtmlHead\Category as CategoryService;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collections\Collection;

use Zend\Log\LoggerInterface,
    Zend\Log\LoggerAwareInterface;

/**
 *
 */
class Page implements LoggerAwareInterface
{
    /**
     *
     */
    const TYPE_TITLE       = 'title';
    /**
     *
     */
    const TYPE_KEYWORDS    = 'keywords';
    /**
     *
     */
    const TYPE_DESCRIPTION = 'description';

    /**
     *
     */
    const QUERY_OPT_WITH_NULL_RAW_STRING    = 'with_null_raw_string';
    /**
     *
     */
    const QUERY_OPT_WITHOUT_NULL_RAW_STRING = 'without_null_raw_string';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Category
     */
    protected $categoryService;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param Category                    $service
     */
    public function __construct(EntityManager $em, CategoryService $service)
    {
        $this->em              = $em;
        $this->categoryService = $service;
    }

    /**
     * @return \MCN\Repository\AdvancedHtmlHead
     */
    protected function getRepository()
    {
        return $this->em->getRepository('MCN\Entity\HtmlHead\Page');
    }

    /**
     * @param \Zend\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $name
     *
     * @return PageEntity|null
     */
    public function get($name)
    {
        $options = array(
            'parameters' => array(
                'name:eq' => $name
            ),

            'relations' => array(
                'category'
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
     * @param array $vars
     *
     * @return array
     */
    protected function exportUsableVariables(array $vars)
    {
        $usable = array();

        foreach($vars as $var => $value)
        {
            switch(true)
            {
                case ($value instanceof AbstractObject):

                    $variables = $value->toArray();

                    if ($value instanceof ContainsSEODataInterface) {

                        $properties = array_combine(
                            $value->getPropertiesForSEO(),
                            $value->getPropertiesForSEO()
                        );

                        $variables = array_intersect_key($variables, $properties);
                    }

                    $usable[$var] = $this->exportUsableVariables($variables);
                    break;

                case is_scalar($value):

                    $usable[$var] = gettype($value);
                    break;

                case is_array($value):
                    $usable[$var] = 'list';
                    break;

                case ($value instanceof Collection):
                    $usable[$var] = 'list_object';
                    break;
            }
        }

        return $usable;
    }

    /**
     * @param string $name
     * @param string $category_name
     * @param bool   $pagination
     * @param array  $fields
     * @param array  $variables
     *
     * @return \MCN\Entity\HtmlHead\Page
     */
    public function create($name, $category_name, $pagination, array $variables)
    {
        // Get the category
        $category = $this->categoryService->get($category_name);

        // Didn't exist
        if (! $category) {

            // create it
            $category = $this->categoryService->create($category_name);
        }

        // Get available variables
        $available_variables = $this->exportUsableVariables($variables);

        $entity = new PageEntity();
        $entity->fromArray(array(
            'name'       => $name,
            'category'   => $category,
            'variables'  => $available_variables,
            'pagination' => $pagination,
            'test_data'  => $variables
        ));

        // Save the object
        $this->save($entity);

        // Notify the administrator a new page has been added
        $this->logger->alert(
            sprintf(
                'Htmlhead (SEO): a new page has been added with the name "%s" belongs to category "%s"',
                $name,
                $category_name
            ),
            $entity->toArray()
        );

        return $entity;
    }

    public function save(PageEntity $entity)
    {
        if (! $this->em->getUnitOfWork()->isInIdentityMap($entity)) {

            $this->em->persist($entity);
        }

        $this->em->flush();
    }

    /**
     * @param array $options
     * @return array|\MCNCore\Pagination\Pagination
     */
    public function fetchAll(array $options = array())
    {
        return $this->getRepository()
                    ->fetchAll($options);
    }
}