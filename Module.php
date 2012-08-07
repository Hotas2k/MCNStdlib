<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN;
use Zend\Log\Logger,
    Zend\Mvc\MvcEvent,
    Zend\ServiceManager\ServiceManager,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\ModuleManager\Feature\ViewHelperProviderInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use MCNCore\Factory,
    MCNCore\View\Helper as ViewHelper;

/**
 * @category User
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface
{
    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()
          ->getEventManager()
          ->attach(MvcEvent::EVENT_DISPATCH, array($this, 'startLogging'));
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function startLogging(MvcEvent $e)
    {
        $sm = $e->getApplication()
                 ->getServiceManager();

        if ($sm->get('Config')['mcn']['logger']['log_errors']) {

            Logger::registerErrorHandler($sm->get('mcn.logger'));
        }

        if ($sm->get('Config')['mcn']['logger']['log_exceptions']) {

            Logger::registerExceptionHandler($sm->get('mcn.logger'));
        }
    }

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
                    'MCN'     => __DIR__ . '/src/MCN',
                    'MCNCore' => __DIR__ . '/src/MCNCore'
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
            'initializers' => array(
                'mcn.sphinx_client' => function($instance, $sm) {

                    if (is_object($instance)) {

                        if (in_array('MCNCore\Sphinx\SearchTrait', class_uses($instance))) {

                            $instance->setSphinxClient($sm->get('mcn.sphinx_client'));
                        }
                    }
                }
            ),

            'factories' => array(
                'mcn.logger'                  => new Factory\LogFactory(),
                'mcn.memcached'               => new Factory\MemcachedFactory(),
                'mcn.sphinx_client'           => new Factory\SphinxClientFactory(),
                'mcn.object.hydrator'         => function($sm) {

                    return new \MCNCore\Object\Hydrator($sm->get('doctrine.entitymanager.ormdefault'));
                },
                'mcn.validator.object_exists' => function($sm) {

                    $validator = new \MCNCore\Validator\ObjectExists();
                    $validator->setObjectManager($sm->get('doctrine.entitymanager.ormdefault'));

                    return $validator;
                },

                'mcn.service.view'               => function($sm) {

                    return new Service\View(
                        $sm->get('doctrine.entitymanager.ormdefault')
                    );
                },
                'mcn.service.image_library'      => function($sm) {

                    return new Service\ImageLibrary(
                        $sm->get('doctrine.entitymanager.ormdefault'),
                        new Service\ImageLibraryOptions($sm->get('Config')['mcn']['image_library'])
                    );
                },
                'mcn.service.search_storage'     => function($sm) {

                    return new Service\SearchStorage(
                        $sm->get('doctrine.entitymanager.ormdefault')
                    );
                },
                'mcn.service.html_head.page' => function($sm) {

                    return new Service\HtmlHead\Page(
                        $sm->get('doctrine.entitymanager.ormdefault'),
                        $sm->get('mcn.service.html_head.category')
                    );
                },
                'mcn.service.html_head.category' => function($sm) {

                    return new Service\HtmlHead\Category(
                        $sm->get('doctrine.entitymanager.ormdefault')
                    );
                },

                // this aint configurable wtf!?
                'doctrine.cache.memcachedCache' => function($sm) {

                    $cache = new \Doctrine\Common\Cache\MemcachedCache();
                    $cache->setMemcached($sm->get('mcn.memcached'));

                    return $cache;
                },

                // Override the ZDT_DbCollector
                'ZDT_DbCollector' => function($sm) {

                    return new \MCNCore\Object\Profiler(
                        $sm->get('mcn.doctrine_logger')
                    );
                }
            )
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'dynamicHtmlHead'       => function($sm) {

                    return new ViewHelper\DynamicHtmlHead(
                        $sm->getServiceLocator()->get('mcn.service.html_head.page')
                    );
                },

                'image' => function($sm) {
                    return new ViewHelper\Image(
                        $sm->getServiceLocator()->get('mcn.service.image_library')
                    );
                }
            )
        );
    }
}
