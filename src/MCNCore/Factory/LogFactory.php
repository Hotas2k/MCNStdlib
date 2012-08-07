<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Factory;
use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,

    Zend\Log\Logger,
    Zend\Mail\Message,
    Zend\Log\Writer\Mail,
    Zend\Log\LoggerInterface,

    MCNCore\Log\Writer\DoctrineDB;

class LogFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Get the mail configuration
        $configuration = $serviceLocator->get('Config')['mcn']['logger'];

        // Bootstrap emailer service
        $transport = $serviceLocator->get('email_transport');

        $message = new Message();
        $message->setTo($configuration['mail']['to'])
                ->setFrom($configuration['mail']['from']);

        $logger = new Logger();

        // writers
        $logger->addWriter(new DoctrineDB($serviceLocator->get('doctrine.entitymanager.ormdefault')));
        $logger->addWriter(new Mail($message, $transport), Logger::WARN);

        return $logger;
    }
}
