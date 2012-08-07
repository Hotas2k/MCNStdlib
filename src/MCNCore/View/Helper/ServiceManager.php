<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\View\Helper;
use Zend\View\Helper\AbstractHelper,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface;
/**
 *
 */
class ServiceManager extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @param ZendServiceManager
     */
    protected $sm;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    /**
     * @return ZendServiceManager
     */
    public function __invoke($name)
    {
        return $this->sm->getServiceLocator()->get($name);
    }


}