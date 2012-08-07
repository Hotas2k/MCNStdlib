<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Entity\HtmlHead;
use MCNCore\Object\Entity\Behavior,
    MCNCore\Object\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *  name="MCN_htmlhead_categories",
 *  uniqueConstraints={
 *
 *      @ORM\UniqueConstraint(name="name_unique", columns={ "name" })
 *  }
 * )
 * @ORM\Entity(repositoryClass="MCNCore\Object\Entity\Repository")
 */
class Category extends AbstractEntity
{
    use Behavior\TimestampableTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var array
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $keywords;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MCN\Entity\HtmlHead\Page", mappedBy="category")
     */
    protected $pages;

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
        return $this->description;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
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
        return $this->title;
    }
}