<?php
namespace Monty;

/**
 * Interface RequestInterface
 * @package Monty
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
interface RequestInterface
{
    /**
     * Method to retrieve the current request path
     * @return string
     */
    public function path();

    /**
     * Method to retrieve the current request method
     * @return string
     */
    public function requestMethod();

    /**
     * Method to set matched route params from the definition
     * @param array $routeParams
     * @return RequestInterface
     */
    public function updateRouteParams(array $routeParams);

    /**
     * Method to set the return of the previous handler function
     * @param mixed $return
     * @return RequestInterface
     */
    public function setPreviousReturn($return);
}