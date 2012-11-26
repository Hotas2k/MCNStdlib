<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCN\Controller\Plugin;

use Zend\Http\Header\GenericHeader;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * @category MCN
 * @package Controller
 * @subpackage Plugin
 */
class Http extends AbstractPlugin
{
    /**
     * Retrieves the sorting field and direction
     *
     * @param string  $field
     * @param string  $direction
     * @param boolean $fromQuery
     *
     * @return array
     */
    public function getSort($field, $direction, $fromQuery = false)
    {
        if ($fromQuery) {

            $sort = trim($this->controller->params()->fromQuery('sort', null));

        } else {

            $sort = trim($this->controller->params('sort', null));
        }

        if ($sort === null) {

            return array($field, $direction);
        }

        if (substr($sort, 0, 1) == '-') {

            return array(substr($sort, 1), 'DESC');
        }
        return array($sort, 'ASC');
    }

    /**
     * Get the range for the http request
     *
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function getRange($limit = 10, $offset = 0)
    {
        $headers = $this->controller->getRequest()->getHeaders();

        if ($headers->has('Range')) {

            $exp = explode('-', substr($headers->get('Range')->getFieldValue(), 6));

            return array($exp[0], $exp[1] - $exp[0]);
        }

        return array($offset, $limit);
    }

    /**
     * Set the correct response according to the range
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $total
     *
     * @return void
     */
    public function setRange($offset, $limit, $total)
    {
        // construct it
        $header = new GenericHeader('Content-Range', sprintf('%d-%d/%d', $offset, $limit, $total));

        // append the header
        $this->controller->getResponse()->getHeaders()->addHeader($header);
    }
}
