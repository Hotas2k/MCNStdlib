<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCN\View\Helper;

use MCN\Object\AbstractObject;
use Zend\View\Helper\AbstractHelper;

/**
 *
 */
class SlugUrl extends AbstractHelper
{
    /**
     * @param String $name
     * @param mixed  $object
     *
     * @return string
     */
    public function __invoke($name, $object)
    {
        if (is_object($object) && method_exists($object, 'toArray')) {

            $object = $object->toArray();
        }

        $slug = isSet($object['url_slug']) ? $object['url_slug'] : 'no-url-slug-specified';

        $parameters = array_merge(
            $object,
            array(
                'slug' => $slug
            )
        );

        return $this->getView()->url($name, $parameters);
    }
}
