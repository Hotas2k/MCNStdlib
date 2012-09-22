<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Object;
use Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @category MCN
 * @package Object
 */
abstract class AbstractRepository
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    protected $metadata;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    final public function __construct(ObjectManager $manager, ClassMetadata $metadata)
    {
        $this->manager  = $manager;
        $this->metadata = $metadata;
    }

    public function getClassMetadata()
    {
        return $this->metadata;
    }
}
