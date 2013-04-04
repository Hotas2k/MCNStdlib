<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 */

namespace MCNStdlib\Object\Entity\Behavior;

use DateTime;

/**
 * Class SoftDeleteTrait
 * @package MCNStdlib\Object\Entity\Behavior
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
