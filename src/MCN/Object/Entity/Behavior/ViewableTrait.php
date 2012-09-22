<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Object\Entity\Behavior;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @category MCN
 * @package Object
 * @subpackage EntityBehavior
 */
trait ViewableTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MCN\Entity\View")
     */
    protected $views;

    public function addView($entity)
    {
        $this->views->add($entity);
    }
}
