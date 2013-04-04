<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNStdlib\View\Helper;

use Zend\Mvc\Router\RouteMatch;
use Zend\View\Helper\AbstractHelper;

/**
 * Class SortUrl
 * @package MCNStdlib\View\Helper
 */
class SortUrl extends AbstractHelper
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @param \Zend\Mvc\Router\RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function __invoke($field)
    {
        /**
         * @var $helper \Zend\View\Helper\Url
         */
        $helper = $this->getView()->plugin('url');

        // Current sort
        $direction   = '';
        $route_field = $this->routeMatch->getParam('sort');

        // descending
        if (substr($route_field, 0, 1) == '-') {

            // turn the array into something a bit more useful
            $direction   = '-';
            $route_field = substr($route_field, 1);
        }

        // do some sorting
        if ($route_field == $field) {

            $direction = $direction == '-' ? '' : '-';
        }

        // invoke the url helper
        return $helper(null, array('page' => 1, 'sort' => $direction . $field), array(), true);
    }
}
