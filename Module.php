<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN;

use Zend\Log\Logger;
use Zend\ServiceManager\ServiceManager;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\Router\RouteMatch;
use MCN\View\Helper as ViewHelper;

/**
 * @category User
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface
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
                    'MCN' => __DIR__ . '/src/MCN'
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
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'mcn.object.hydrator'         => function($sm) {

                    return new Object\Hydrator($sm->get('doctrine.entitymanager.ormdefault'));
                },

                'mcn.validator.object_exists' => function($sm) {

                    $validator = new Validator\ObjectExists();
                    $validator->setObjectManager($sm->get('doctrine.entitymanager.ormdefault'));

                    return $validator;
                },

                'mcn.service.view'               => function($sm) {

                    return new Service\View(
                        $sm->get('doctrine.entitymanager.ormdefault')
                    );
                },

                'mcn.service.search_storage'     => function($sm) {

                    return new Service\SearchStorage(
                        $sm->get('doctrine.entitymanager.ormdefault')
                    );
                }
            )
        );
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
                'sortUrl' => function($sm) {

                    $helper = new View\Helper\SortUrl();

                    $routeMatch = $sm->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();

                    if ($routeMatch instanceof RouteMatch) {

                        $helper->setRouteMatch($routeMatch);
                    }

                    return $helper;
                }
            )
        );
    }
}
