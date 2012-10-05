<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */


namespace MCN\Controller\Plugin;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * @category MCN
 * @package Controller
 * @subpackage Plugin
 */
class SearchStorage extends AbstractPlugin
{
    public function __invoke($route, $namespace)
    {
        $controller = $this->getController();
        if (! $controller instanceof ServiceLocatorAwareInterface) {

            throw new \RuntimeException('Controller should implement ServiceLocatorAwareInterface');
        }

        $request = $controller->getRequest();
        $service = $controller->getServiceLocator()
                              ->get('mcn.service.search_storage');

        if ($request->isPost()) {

            $parameters = $request->getPost()
                                  ->toArray();

            $hash = md5(serialize($parameters));

            $result = $service->has($hash, $namespace);

            if (! $result) {

                $result = $service->insert($parameters, $hash, $namespace);
            }

            $this->getController()
                 ->redirect()
                 ->toRoute($route, array('id' => $result->getId()));

        } else {

            $id = $this->getController()
                       ->getEvent()
                       ->getRouteMatch()
                       ->getParam('id');

            // Static search page
            if (empty($id)) {

                return array();
            }

            $result = $service->get($id, $namespace);

            if ($result) {

                return $result->getParameters();
            }

            return array();
        }
    }
}
