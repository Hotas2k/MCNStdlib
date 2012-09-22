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
    MCN\Object\Entity\Behavior\ViewableInterface;

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
        $data = array(
            'target_id'   => $object->getId(),
            'target_type' => $object->getViewTargetType(),
            'hash'        => md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])
        );

        $result = $this->getRepository()
                       ->fetchOne(array('parameters' => $data));

        if (! $result) {

            $view = new ViewEntity();
            $view->fromArray($data);

            $object->addView($view);

            $this->em->persist($view);
            $this->em->flush();
        }
    }
}
