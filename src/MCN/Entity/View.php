<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Entity;
use Doctrine\ORM\Mapping as ORM,
    MCN\Object\Entity\AbstractEntity;


/**
 * @category MCN
 * @package Object
 * @subpackage PredefinedEntity
 *
 * @ORM\Table(name="MCN_doctrine_view", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="primary_keys", columns={ "target_type", "target_id", "hash" })
 * })
 * @ORM\Entity(repositoryClass="MCN\Repository\View")
 */
class View extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $target_type;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $target_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40)
     */
    protected $hash;


    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param int $target_id
     */
    public function setTargetId($target_id)
    {
        $this->target_id = $target_id;
    }

    /**
     * @return int
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * @param string $target_type
     */
    public function setTargetType($target_type)
    {
        $this->target_type = $target_type;
    }

    /**
     * @return string
     */
    public function getTargetType()
    {
        return $this->target_type;
    }
}
