<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Entity;
use Doctrine\ORM\Mapping as ORM,
    MCNCore\Object\Entity\Behavior,
    MCNCore\Object\Entity\AbstractEntity;

/**
 * @ORM\Table(name="MCN_doctrine_images")
 * @ORM\Entity(repositoryClass="MCNCore\Object\Entity\Repository")
 */
class Image extends AbstractEntity
{
    use Behavior\TimestampableTrait;

    //<editor-fold desc="doctrine property mapping">
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
     * @ORM\Column(type="string", length=25)
     */
    protected $target_type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $target_position;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $resolutions = array();

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    protected $order;
    //</editor-fold>

    /**
     * @param string $resolution
     *
     * @return bool
     */
    public function hasResolution($resolution)
    {
        return array_key_exists($resolution, $this->resolutions);
    }

    public function getResolution($resolution)
    {
        return $this->resolutions[$resolution];
    }

    public function addResolution($resolution, $path)
    {
        $this->resolutions[$resolution] = $path;
    }

    //<editor-fold desc="Getters & setters">
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param array $resolutions
     */
    public function setResolutions($resolutions)
    {
        $this->resolutions = $resolutions;
    }

    /**
     * @return array
     */
    public function getResolutions()
    {
        return $this->resolutions;
    }

    /**
     * @param string $target_position
     */
    public function setTargetPosition($target_position)
    {
        $this->target_position = $target_position;
    }

    /**
     * @return string
     */
    public function getTargetPosition()
    {
        return $this->target_position;
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
    //</editor-fold>
}