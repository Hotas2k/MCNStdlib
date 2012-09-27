<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN\Repository;

use MCN\Object\Entity\Repository;

/**
 * @category MCN
 * @package Repository
 */
class View extends Repository
{
    /**
     * Inserts a new view to the database if one does not already exist
     *
     * @param array $values
     */
    public function insert(array $values)
    {
        $stmt = $this->manager->getConnection()
                              ->prepare('INSERT IGNORE INTO MCN_doctrine_view VALUES (null, :target_type, :target_id, :hash)');

        $stmt->execute($values);
    }
}
