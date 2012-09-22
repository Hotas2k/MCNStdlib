<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN;

use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use MCN\View\Helper as ViewHelper;

/**
 * @category User
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface
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

                    return new \MCN\Object\Hydrator($sm->get('doctrine.entitymanager.ormdefault'));
                },

                'mcn.validator.object_exists' => function($sm) {

                    $validator = new \MCN\Validator\ObjectExists();
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
                },

                // this aint configurable wtf!?
                'doctrine.cache.memcachedCache' => function($sm) {

                    $cache = new \Doctrine\Common\Cache\MemcachedCache();
                    $cache->setMemcached($sm->get('mcn.memcached'));

                    return $cache;
                }
            )
        );
    }
}
