<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 */

namespace MCN\Object\Entity\Behavior;

use DateTime;

/**
 * @category MCN
 * @package Object
 * @subpackage EntityBehavior
 */
trait SoftDeleteTrait
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @\Zend\Form\Annotation\Exclude
     */
    protected $deleted_at = null;

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param mixed $timestamp
     *
     * @return self
     */
    public function setDeletedAt($timestamp = null)
    {
        if (! $timestamp instanceof DateTime) {

            $timestamp = new DateTime($timestamp);
        }

        $this->deleted_at = $timestamp;

        return $this;
    }
}
