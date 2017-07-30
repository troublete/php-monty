<?php
namespace Monty;

use Monty\Exception\HandlerCouldNotBeIntegratedException;
use Monty\Exception\UnhandledRequestException;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class Application
 * @package Monty
 */
class Application
{
    const PREPEND = 0;
    const APPEND = 1;

    /**
     * @var RouteHandler
     */
    protected $routeHandler;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var boolean
     */
    protected $responseSet = false;

    /**
     * @var array
     */
    protected $prepend = [];

    /**
     * @var array
     */
    protected $append = [];

    /**
     * Application constructor.
     */
    public function __construct(Response $response = null)
    {
        $this->routeHandler = new RouteHandler();
        $this->request = new Request(HttpRequest::createFromGlobals());
        $this->response = new Response('Oh, well hellow there.');

        if ($response !== null) {
            $this->response = $response;
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Monty\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Method to handle a request.
     * @param array $methods
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function handle(
        array $methods = [
            HttpRequest::METHOD_HEAD,
            HttpRequest::METHOD_GET,
            HttpRequest::METHOD_POST,
            HttpRequest::METHOD_PUT,
            HttpRequest::METHOD_PATCH,
            HttpRequest::METHOD_DELETE,
            HttpRequest::METHOD_PURGE,
            HttpRequest::METHOD_OPTIONS,
            HttpRequest::METHOD_TRACE,
            HttpRequest::METHOD_CONNECT
        ],
        string $route = null,
        ...$handlers
    )
    {
        // check if request method is registered for handle
        if (
            in_array(
                $this->request->requestMethod(),
                $methods
            )
        )
        {
            foreach ($this->routeHandler->parseRoute($route) as $regex) {

                // check if regex does match with request uri
                $regexMatched = preg_match($regex, $this->request->requestUri(), $matches);
                if ($route === null || $regexMatched) {

                    foreach ($matches as $param => $value) {
                        if (is_numeric($param))
                            unset($matches[$param]);
                    }

                    $this->request->updateRouteParams($matches);

                    // process all handlers
                    foreach (
                        array_merge($this->prepend, $handlers, $this->append) as $handler // merge all request handler
                    )
                    {
                        if (is_callable($handler)) {
                            $response = call_user_func($handler, $this->request, $this->response, ...array_values($matches));
                            $this->request->setPreviousReturn($response);

                            if ($response instanceof Response) {
                                if ($this->responseSet === false) {
                                    $this->response = $response;
                                    $this->responseSet = true;
                                }
                            }
                        }
                    }

                    if ($this->responseSet === true) {
                        return $this->response->send();
                    }

                    // throw exception if no handler returned a response
                    if ($route !== null)
                        throw new UnhandledRequestException();
                }
            }
        }

        return 0;
    }

    /**
     * Method to handle ANY request
     * @param string|null $route
     * @param array ...$handlers
     * return mixed{Response
     */
    public function all(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_HEAD,
                HttpRequest::METHOD_GET,
                HttpRequest::METHOD_POST,
                HttpRequest::METHOD_PUT,
                HttpRequest::METHOD_PATCH,
                HttpRequest::METHOD_DELETE,
                HttpRequest::METHOD_PURGE,
                HttpRequest::METHOD_OPTIONS,
                HttpRequest::METHOD_TRACE,
                HttpRequest::METHOD_CONNECT
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a GET request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function get(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_GET
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a POST request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function post(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_POST
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a PUT request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function put(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_PUT
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a PATCH request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function patch(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_PATCH
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a DELETE request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function delete(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_DELETE
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a OPTIONS request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function options(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_OPTIONS
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to handle a HEAD request
     * @param string|null $route
     * @param array ...$handlers
     * @return mixed|Response
     */
    public function head(
        string $route = null,
        ...$handlers
    )
    {
        return $this->handle(
            [
                HttpRequest::METHOD_HEAD
            ],
            $route,
            ...$handlers
        );
    }

    /**
     * Method to apply middleware handlers to the application lifecycle
     * @param int $placing
     * @param array ...$handlers
     * @return $this
     */
    public function middleware(
        $placing = self::PREPEND,
        ...$handlers
    )
    {
        switch($placing) {
            case self::PREPEND:
                $this->prepend = array_merge($this->prepend, $handlers);
                break;
            case self::APPEND:
                $this->append = array_merge($this->append, $handlers);
                break;
            default:
                throw new HandlerCouldNotBeIntegratedException();
        }

        return $this;
    }

    /**
     * Method to prepend middleware handlers before the actual request handle
     * @param array ...$handlers
     * @return Application
     */
    public function before(...$handlers)
    {
        return $this->middleware(self::PREPEND, ...$handlers);
    }

    /**
     * Method to append middleware handlers after the request handle
     * @param array ...$handlers
     * @return Application
     */
    public function after(...$handlers)
    {
        return $this->middleware(self::APPEND, ...$handlers);
    }
}