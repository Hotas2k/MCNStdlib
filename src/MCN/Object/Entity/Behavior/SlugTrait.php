<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN\Object\Entity\Behavior;

use MCN\Object\Exception;

/**
 * @category MCN
 * @package Object
 * @subpackage EntityBehavior
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
     * @throws \MCN\Object\Exception\RuntimeException
     * @return void
     */
    public function onDatabaseEventUpdateUrlSlug()
    {
        $string = $this->getStringToSlug();

        if (empty($string)) {

            throw new Exception\RuntimeException('Cannot update slug to NULL');
        }

        $this->url_slug = self::convertToSlug($string);
    }

    public function setSlugUrl($u)
    {
        $this->url_slug = $u;
    }

    /**
     * Converts a string to something a bit more URL friendly
     *
     * @static
     *
     * @param $string
     *
     * @return mixed|string
     */
    static public function convertToSlug($string)
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '_', $clean);

        return $clean;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract protected function getStringToSlug();
}
