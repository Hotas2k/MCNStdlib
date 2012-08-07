<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\View\Helper;
use MCNCore\Object\AbstractObject,
    Zend\View\Helper\AbstractHelper,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 *
 */
class SlugUrl extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $sm;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SlugUrl
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }

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

        $parameters = array_merge(
            $object,
            array(
                'slug' => $object['url_slug']
            )
        );

        return $this->getView()
                    ->url($name, $parameters);
    }
}