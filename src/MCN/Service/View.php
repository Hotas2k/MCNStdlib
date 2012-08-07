<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Service;
use Doctrine\ORM\EntityManager,
    MCN\Entity\View as ViewEntity,
    MCNCore\Object\Entity\Behavior\ViewableInterface;

class View
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \MCN\Repository\View
     */
    protected function getRepository()
    {
        return $this->em->getRepository('MCN\Entity\View');
    }

    public function addViewTo(ViewableInterface $object)
    {
        $this->getRepository()
             ->insert(
                array(
                     'target_id'   => $object->getId(),
                     'target_type' => $object->getViewTargetType(),
                     'hash'        => md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])
                )
             );
    }

}