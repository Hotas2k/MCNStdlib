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

            if ($metadata->hasAssociation($field)) {

                $mapping = $metadata->getAssociationMapping($field);

                if ($mapping['type'] & ClassMetadataInfo::TO_ONE) {

                    if (! $value instanceof AbstractObject) {

                        throw new Exception\InvalidArgumentException('Invalid Abstract object given as relation.');
                    }

                    $targetKeyColumns = $mapping['sourceToTargetKeyColumns'];

                    if (count($targetKeyColumns) > 1) {

                        throw new \Exception('Not yet implemented');
                    }

                    $value = $value[current($targetKeyColumns)];
                }

            } else {

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
            if (empty($value)) continue;

            if ($this->metadata->hasAssociation($field)) {

                $association = $this->metadata->getAssociationMapping($field);

                switch($association['type'])
                {
                    case ClassMetadataInfo::ONE_TO_MANY:
                    case ClassMetadataInfo::MANY_TO_MANY:
                        if (! empty($value)) {

                            if (! current($value) instanceof AbstractObject) {

                                foreach ($value as &$v) {

                                    $v = $this->em->find($association['targetEntity'], $v);
                                }
                            }
                        }

                        $this->toMany($object, $field, $value, $association);
                        break;

                    case ClassMetadataInfo::ONE_TO_ONE:
                    case ClassMetadataInfo::MANY_TO_ONE:

                        if (! $value instanceof AbstractObject) {

                            $value = $this->em->find($association['targetEntity'], $value);
                        }

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
;
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
