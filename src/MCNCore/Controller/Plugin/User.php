<?php
/**
 * @namespace
 */
namespace MCNCore\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class User extends AbstractPlugin
{
    public function __invoke()
    {
        return $this->getController()
                    ->getServiceLocator()
                    ->get('user_service_auth')
                    ->getIdentity();
    }
}