<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCNStdlib\Object\Entity\Behavior;

use BaconStringUtils\Slugifier;
use MCNStdlib\Object\Exception;

/**
 * Class SlugTrait
 * @package MCNStdlib\Object\Entity\Behavior
 */
trait SlugTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @\Zend\Form\Annotation\Exclude
     */
    protected $url_slug;

    /**
     * @return string
     */
    public function getUrlSlug()
    {
        return $this->url_slug;
    }

    /**
     * When ever a database event occurs update the the slug
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @throws \MCNStdlib\Object\Exception\RuntimeException
     * @return void
     */
    public function onDatabaseEventUpdateUrlSlug()
    {
        $string = $this->getStringToSlug();

        if (empty($string)) {

            throw new Exception\RuntimeException('Cannot update slug to NULL');
        }

        $slugifier = new Slugifier();
        $this->setSlugUrl($slugifier->slugify($string));
    }

    /**
     * @param string $url
     */
    public function setSlugUrl($url)
    {
        $this->url_slug = $url;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract protected function getStringToSlug();
}
