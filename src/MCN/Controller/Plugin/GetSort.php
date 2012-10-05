<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCN\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 *
 */
class GetSort extends AbstractPlugin
{
    /**
     * Retrieves
     *
     * @param string $field
     * @param string $direction
     *
     * @return array
     */
    public function __invoke($field, $direction)
    {
        $sort = $this->controller->params('sort', null);

        if ($sort === null) {

            return array($field => $direction);
        }

        if (substr($sort, 0, 1) == '-') {

            return array(substr($sort, 1) => 'DESC');
        }

        return array($sort => 'ASC');
    }
}