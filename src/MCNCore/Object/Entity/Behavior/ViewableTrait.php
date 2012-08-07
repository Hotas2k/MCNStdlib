<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Object\Entity\Behavior;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @category MCNCore
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
}