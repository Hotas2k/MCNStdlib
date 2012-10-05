<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

namespace MCN\Controller\Plugin;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * @category MCN
 * @package Controller
 * @subpackage Plugin
 */
class Message extends AbstractPlugin
{
    /**
     * Creates a view model that renders a template for displaying messages to the user
     *
     * @param $title
     * @param $message
     * @param null $route
     * @param string $submitLabel
     * @param string $template
     * @return \Zend\View\Model\ViewModel
     */
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
