<?php
namespace Monty;

use Monty\Exception\PropertyCouldNotBeSetException;
use Monty\Exception\PropertyNotAvailableException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class Request
 * @package Monty
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
class Request implements RequestInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var ParameterBag
     */
    protected $routeParams;

    /**
     * @var mixed
     */
    protected $previousReturn;

    /**
     * Request constructor.
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
        $this->container = new ContainerBuilder();
        $this->routeParams = new ParameterBag();
    }

    /**
     * Method to set a new property to the request
     * @param string $id
     * @param mixed $value
     * @return $this
     */
    public function set(...$parameters)
    {
        list($id, $value) = $parameters;

        if (
            !is_string($id)
            || $value === null
            || is_array($value)
        )
        {
            throw new PropertyCouldNotBeSetException();
        }

        switch ($value) {
            case is_object($value):
                $this->container->set(...$parameters);
                break;
            case is_scalar($value):
                $this->request->attributes->set(...$parameters);
                break;
        }

        return $this;
    }

    /**
     * Method to retrieve a service from the container and define what should happen if it does not exist or retrieve a parameter from the request
     * @param string $id
     * @param mixed $default
     * @return mixed
     */
    public function get(...$parameters)
    {
        list($id) = $parameters;

        if (!is_string($id)) {
            throw new PropertyNotAvailableException();
        }

        $response = $this->request->attributes->get(...$parameters);

        if ($response === null) {
            $response = $this->container->get(...$parameters);
        }

        return $response;
    }

    /**
     * Method to retrieve the route parameters
     * @return ParameterBag
     */
    public function parameters()
    {
        return $this->routeParams;
    }

    /**
     * Method to retrieve the $_GET parameters
     * @return ParameterBag
     */
    public function query()
    {
        return $this->request->query;
    }

    /**
     * Method to retrieve the $_POST parameters
     * @return ParameterBag
     */
    public function request()
    {
        return $this->request->request;
    }

    /**
     * Method to retrieve the $_FILES parameters
     * @return \Symfony\Component\HttpFoundation\FileBag
     */
    public function files()
    {
        return $this->request->files;
    }

    /**
     * Method to retrieve the request method
     * @return string
     */
    public function requestMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * Method to retrieve the URI
     * @return string
     */
    public function path()
    {
        return $this->request->getRequestUri();
    }

    /**
     * Method to retrieve the base URL
     * @return string
     */
    public function httpHost()
    {
        return $this->request->getSchemeAndHttpHost();
    }

    /**
     * Method to retrieve the client IP
     * @return null|string
     */
    public function clientIp()
    {
        return $this->request->getClientIp();
    }

    /**
     * Method to retrieve the requested Content-type
     * @return null|string
     */
    public function contentType()
    {
        return $this->request->getContentType();
    }

    /**
     * Method to check if the request method is a specific request
     * @param string $method
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->request->isMethod($method);
    }

    /**
     * Method to check if the request is secure
     * @return bool
     */
    public function isSecure()
    {
        return $this->request->isSecure();
    }

    /**
     * Method to retrieve the raw request for accessing methods not available in the public interface
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRawRequest()
    {
        return $this->request;
    }

    /**
     * Method to retrieve the raw container to access methods not covered by the common interface
     * @return ContainerBuilder
     */
    public function getRawContainer()
    {
        return $this->container;
    }

    /**
     * Method to update the route parameters
     * @param array $params
     * @return $this
     */
    public function updateRouteParams(array $params)
    {
        $this->routeParams = new ParameterBag($params);
        return $this;
    }

    /**
     * Method to retrieve the return of the previous handler
     * @return mixed
     */
    public function previousReturn()
    {
        return $this->previousReturn;
    }

    /**
     * Method to set the return of the previous handler
     * @param mixed $previousReturn
     * @return $this
     */
    public function setPreviousReturn($previousReturn)
    {
        $this->previousReturn = $previousReturn;
        return $this;
    }
}