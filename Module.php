<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCNStdlib;

use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Router\RouteMatch;
use MCNStdlib\View\Helper as ViewHelper;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Module
 * @package MCNStdLib
 */
class Module
{
    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'sortUrl' => function(ServiceLocatorInterface $sm) {

                    $helper = new View\Helper\SortUrl();

                    $routeMatch = $sm->getServiceLocator()
                                     ->get('application')
                                     ->getMvcEvent()
                                     ->getRouteMatch();

                    if ($routeMatch instanceof RouteMatch) {

                        $helper->setRouteMatch($routeMatch);
                    }

                    return $helper;
                }
            )
        );
    }
}
