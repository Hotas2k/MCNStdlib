<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Entity\HtmlHead;
use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\Collection;

use MCNCore\Object\Entity\Behavior,
    MCNCore\Object\Entity\AbstractEntity;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="MCNCore\Object\Entity\Repository")
 * @ORM\Table(name="MCN_htmlhead_pages",
 *  uniqueConstraints = {
 *      @ORM\UniqueConstraint(name="unique_page_name", columns={"name" })
 *  }
 * )
 */
class Page extends AbstractEntity
{
    use Behavior\TimestampableTrait;

    //<editor-fold desc="doctrine property mapping">
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $category_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $keywords;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $pagination;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $variables;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $test_data;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="MCN\Entity\HtmlHead\Category", inversedBy="pages")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;
    //</editor-fold>

    /**
     * @param \MCN\Entity\HtmlHead\Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \MCN\Entity\HtmlHead\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return boolean
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->description)) {

            return $this->getCategory()->getDescription();
        }

        return $this->description;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        if (empty($this->keywords)) {

            return $this->getCategory()->getKeywords();
        }

        return $this->keywords;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->title)) {

            return $this->getCategory()->getTitle();
        }

        return $this->title;
    }

    /**
     * @param array $test_data
     */
    public function setTestData($test_data)
    {
        $this->test_data = $test_data;
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return $this->test_data;
    }
}