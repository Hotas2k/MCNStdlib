<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Repository;
use MCNCore\Object\Entity\Repository;

class View extends Repository
{
    public function insert(array $values)
    {
        $stmt = $this->manager->getConnection()
                              ->prepare('INSERT IGNORE INTO MCN_doctrine_view VALUES (null, :target_type, :target_id, :hash)');

        $stmt->execute($values);
    }
}