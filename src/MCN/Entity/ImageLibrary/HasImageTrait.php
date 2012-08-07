<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Entity\ImageLibrary;
use Doctrine\Common\Collections\ArrayCollection;

trait HasImageTrait
{
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MCN\Entity\Image")
     * @ORM\JoinColumn(name="id", referencedColumnName="target_id")
     */
    protected $images;

    public function getImages()
    {
        if ($this->images == null) {

            $this->images = new ArrayCollection();
        }

        return $this->images;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $images
     *
     * @return HasImageTrait
     */
    public function setImages(ArrayCollection $images)
    {
        $this->images = $images;

        return $this;
    }
}