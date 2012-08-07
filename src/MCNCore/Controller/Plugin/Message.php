<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Controller\Plugin;
use Zend\View\Model\ViewModel,
    Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Message extends AbstractPlugin
{
    public function __invoke($title, $message, $route = null, $submitLabel = 'Gå tillbaka', $template = 'message')
    {
        $vm = new ViewModel();
        $vm->setTemplate($template)
           ->setVariables(
               array(
                    'route'       => $route,
                    'title'       => $title,
                    'message'     => $message,
                    'submitLabel' => $submitLabel
               )
           );

        return $vm;
    }
}