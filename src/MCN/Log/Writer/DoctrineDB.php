<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Log\Writer;
use Zend\Log\Writer\AbstractWriter,
    Doctrine\Common\Persistence\ObjectManager;

/**
 * @category MCN
 * @package Log
 * @subpackage DoctrineWriter
 */
class DoctrineDB extends AbstractWriter
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * @var string
     */
    protected $entityClass = 'MCN\Entity\Log';

    /**
     * @var string
     */
    protected $documentClass = 'MCN\Document\Log';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @return mixed
     * @throws \Zend\Log\Exception\RuntimeException
     */
    protected function getNewObject()
    {
        if ($this->om instanceof \Doctrine\ORM\EntityManager) {

            return new $this->entityClass;
        } else if ($this->om instanceof \Doctrine\MongoDB\DocumentManager) {

            return new $this->documentClass;
        } else {

            // Uhoh?
            throw new \Zend\Log\Exception\RuntimeException(
                sprintf('Unimplemented object manager "%s" specified.', get_class($this->om))
            );
        }
    }

    /**
     * Search the event extra to see if a user id has been specified.
     *
     * @param array $extra
     * @return int|null
     */
    protected function getUidFromEventExtra(array $extra)
    {
        if (isSet($extra['user_id'])) {

            return (int) $extra['user_id'];

        } else if (isSet($extra['user'])) {

            if ($extra['user'] instanceof UserEntity) {

                return $extra['user']->getId();

            } else if (is_int($extra['user'])) {

                return $extra['user'];
            }
        }

        return null;
    }

    /**
     * Write a message to the log
     *
     * @param array $event log data event
     *
     * @return void
     */
    protected function doWrite(array $event)
    {
        $uid = $this->getUidFromEventExtra($event['extra']);

        $object = $this->getNewObject();
        $object->fromArray(
            array(
                 'uid'      => $uid,
                 'message'  => $event['message'],
                 'priority' => $event['priority'],
                 'extra'    => $event['extra']
            )
        );

        $this->om->persist($object);
        $this->om->flush($object);
    }
}
