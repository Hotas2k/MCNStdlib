<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Factory;
use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

use SphinxClient;

class SphinxClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration = $serviceLocator->get('Config')['mcn']['sphinx'];

        $client = new SphinxClient();
        $client->setServer($configuration['host'], $configuration['port']);

        return $client;
    }
}
