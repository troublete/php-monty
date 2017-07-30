<?php
namespace Monty;

/**
 * Interface RouteHandlerInterface
 * @package Monty
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
interface RouteHandlerInterface
{
    /**
     * Method to parse the route defined by the handler definition and create an array of regular expressions which will
     * be tried to be matched against the request path and identify and match the route params
     * @param string $routeDefinition
     * @return array
     */
    public function parseRoute(string $routeDefinition);
}