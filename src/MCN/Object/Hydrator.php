<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN\Object;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * @category MCN
 * @package Object
 */
class Hydrator implements HydratorInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        $metadata = $this->em->getClassMetadata(get_class($object));

        $data = array();

        foreach($object->toArray() as $field => $value)
        {
            if (empty($value)) continue;

            // Check if the value if a datetime and then format accordingly
            if ($value instanceof Datetime) {

                switch($metadata->getTypeOfField($field))
                {
                    case Type::DATE:
                        $value = $value->format('Y-m-d');
                        break;

                    case Type::DATETIME:
                        $value = $value->format('Y-m-d H:i:s');
                        break;

                    case Type::TIME:
                        $value = $value->format('H:i:s');
                        break;
                }
            }

            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        if (! $object instanceof AbstractObject) {

            throw new Exception\LogicException(
                sprintf('The second parameter passed must be an instance of abstract object. "%s" passed', get_class($object))
            );
        }

        $this->metadata = $this->em->getClassMetadata(get_class($object));

        foreach($data as $field => $value)
        {
            if ($this->metadata->hasAssociation($field)) {

                $association = $this->metadata->getAssociationMapping($field);

                if (! $value instanceof AbstractObject) {

                    $value = $this->em->getReference($association['targetEntity'], $value);
                }

                switch($association['type'])
                {
                    case ClassMetadataInfo::ONE_TO_MANY:
                    case ClassMetadataInfo::MANY_TO_MANY:
                        $this->toMany($object, $field, $value, $association);
                        break;

                    case ClassMetadataInfo::ONE_TO_ONE:
                    case ClassMetadataInfo::MANY_TO_ONE:
                        $this->toOne($object, $field, $value, $association);
                        break;
                }
            }

            if ($this->metadata->hasField($field)) {

                $type = $this->metadata->getTypeOfField($field);

                switch($type)
                {
                    case Type::DATE:
                    case Type::DATETIME:
                        if (! $value instanceof DateTime) {

                            $value = DateTime::createFromFormat('U', strtotime($value));
                        }

                        $object->offsetSet($field, $value);
                        break;

                    default:
                        $object->offsetSet($field, $value);
                        break;
                }
            }
        }

        return $object;
    }

    /**
     * @param AbstractObject $object
     * @param string         $field
     * @param mixed          $value
     * @param array          $association
     * @return void
     */
    protected function toOne(AbstractObject $object, $field, AbstractObject $value, array $association)
    {
        $identifiers = array();

        foreach($association['sourceToTargetKeyColumns'] as $column) {

            // Empty object specified
            if (empty($value[$column])) {

                return null;
            }

            $identifiers[$column] = $value[$column];
        }

        $object->offsetSet($field, $this->em->getReference(get_class($value), $identifiers));
    }

    /**
     * @param AbstractObject $object
     * @param string         $field
     * @param mixed          $value
     * @param array          $association
     * @return void
     */
    protected function toMany(AbstractObject $object, $field, $value, array $association)
    {
        if (!is_array($value)) {

            $value = (array) $value;
        }

        $values = new ArrayCollection();
        foreach($value as $v) {

            $identifiers = array();

            foreach($association['relationToTargetKeyColumns'] as $column) {

                // Empty object specified
                if (empty($v[$column])) {

                    continue 2;
                }

                $identifiers[$column] = $v[$column];
            }

            $values[] = $this->em->getReference(get_class($v), $identifiers);
        }

        $object->offsetSet($field, $values);
    }
}
