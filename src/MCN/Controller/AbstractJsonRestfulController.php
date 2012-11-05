<?php
/**
 * @author ANtoine Hedgecock <antoine@pmg.se
 */

namespace MCN\Controller;

use JsonSerializable;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractController;

use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

use Zend\View\Model\JsonModel;

/**
 * @category MCN
 * @package Controller
 */
abstract class AbstractJsonRestfulController extends AbstractController
{
    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * Return list of resources
     *
     * @return mixed
     */
    abstract public function getList();

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @return mixed
     */
    abstract public function get($id);

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return mixed
     */
    abstract public function create($data);

    /**
     * Update an existing resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    abstract public function update($id, $data);

    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @return mixed
     */
    abstract public function delete($id);

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws Exception\InvalidArgumentException
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new Exception\InvalidArgumentException('Expected an HTTP request');
        }

        return parent::dispatch($request, $response);
    }

    /**
     * Handles the request
     *
     * @param MvcEvent $e
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $request    = $e->getRequest();
        $routeMatch = $e->getRouteMatch();


        // handle the request
        switch(strtoupper($request->getMethod()))
        {
            case HttpRequest::METHOD_GET:

                $id = $routeMatch->getParam('id');

                if (! $id) {

                    $response = $this->getList();

                } else {

                    $response = $this->get($id);
                }
                break;

            case HttpRequest::METHOD_POST:

                $response = $this->create(json_decode($request->getContent(), true));
                break;

            case HttpRequest::METHOD_PUT:

                $id = $routeMatch->getParam('id');

                // does not work without an empty id
                if (! $id) {

                    throw new Exception\DomainException('HTTP Put requires an identifier to be specified');
                }

                $response = $this->update($id, json_decode($request->getContent(), true));
                break;

            case HttpRequest::METHOD_DELETE:

                $id = $routeMatch->getParam('id');

                if (! $id) {

                    throw new Exception\DomainException('HTTP Delete requires a identifier to be specified');
                }

                $response = $this->delete($id);
                break;
        }

        if ($response instanceof JsonSerializable) {

            $response = $response->jsonSerialize();
        }

        $e->setResult(new JsonModel($response));
    }
}

