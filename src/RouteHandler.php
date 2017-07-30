<?php
namespace Monty;

use FastRoute\RouteParser\Std;

/**
 * Class Router
 * @package Monty
 */
class RouteHandler
{
    /**
     * @var Std
     */
    protected $parser;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->parser = new Std();
    }

    /**
     * Method to parse a route and return a regex collection for it
     * @param string $route
     * @return array
     */
    public function parseRoute(string $route)
    {
        $parsedRoute = $this->parser->parse($route);
        $regexCollection = $this->createRegexCollection($parsedRoute);

        // reverse so the most "advanced" route regex will be used first to guarantee an early exit
        return array_reverse($regexCollection);
    }

    /**
     * Method to create an array of regular expressions for a parsed routing
     * @param array $routes
     * @return array
     */
    protected function createRegexCollection(array $routes)
    {
        $routes = array_map(
            function ($route) {
                $regex = implode(
                    '',
                    array_map(
                        function ($chunk) {
                            if (is_string($chunk))
                                return $chunk;

                            list($name, $regex) = $chunk;
                            return "(?<$name>$regex)";
                        },
                        $route
                    )
                );


                return "@$regex@";
            },
            $routes
        );

        return $routes;
    }
}