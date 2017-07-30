# Monty

### *Sinatra-like framework, focused on fast response and reliability.*

[![Build Status](https://travis-ci.org/troublete/monty.svg?branch=master)](https://travis-ci.org/troublete/monty)

## Example Setup

```
$ composer require troublete/monty
```

```php
<?php
require_once 'path/to/vendor/autoload.php';

$application = new \Monty\Application();

$application->get(
    '/request[/{someId}]',
    function (\Monty\Request $req, \Monty\Response $res, $someId) {
        
        // do some awesome stuff
        
        return new \Symfony\Component\HttpFoundation\JsonResponse([]);
    }
);
```

## Routing

The route parsing is based on the [FastRoute](https://github.com/nikic/FastRoute) package created be @nikic, so for the
most part it should be possible to define routes as specified by FastRoute.
Route matching is done via **PCRE** with delimiter set to `@`, so be aware when setting user defined regex parameter matches with `@`.
Route definitions allow variable parts at the end of a definition marked with `[]`. Since this is possible the matching
will try multiple regular expressions in order of descendant complexity and will return on the first pattern match.

Valid routing
```
/search/{searchId} => routing with parameter
/search/{searchId:\d+} => routing with parameter with defined regex
/search[/{searchId}] => routing with optional parameter
/search/index[es] => routing with optional part
```

Invalid routing
```
/search/index[es]/{searchId} => optional chunk in the middle
```

## Request Handling

Handling definitions will be processed in order of registration as soon as one matches with the received request it will
dispatch, return and therefore close the process.  

In addition Monty is designed to be request and response centric, following the dogma that one request to an application
will be handled once so everything needed during the lifecycle is included (or should be appended) in the request or response
object. 

Handlers on a definition are an array of `callable`'s and will be executed synchronously in order on definition match. 
Middlewares registered to run before or after are integrated in this "call stack".
 
If a response object is returned in a handler it is interpreted as the process response and can't be reset (but modified).

## Application

The application is the main component of Monty. It will handle the registration of route handlers, middlewares and additional
setup of request and response. It generally contains four different use-cases. Accessing the request or response object
of the current request process, registering route handlers and registering middlewares which will be executed during
the lifecycle.

In addition to the use-case methods it also contains an interface of alias methods to make the code your write a lot more
understandable and sleek.

### Methods

#### $app->handle($methods, $route, ...$handlers)

This method registers new request handlers for a specific route in regard to an collection of request methods on which
should be dispatched. 

##### Arguments

| Argument | Type | Description |
|---|---|---|
| $methods | *string[]* | Collection of request methods in uppercase. |
| $route | *string* | The route to which the handlers are registered. |
| ...$handlers | *callable[]* | Collection of handlers which will be executed. |

##### Example

```php
// ...
$app->handle(
    ['GET'], 
    '/index', 
    function ($req, $res) { /*...*/ }, 
    function ($req, $res) { /*...*/ }, 
    function ($req, $res) { /*...*/ }
    // ...
);
// ...
```

##### Aliases

| Method | Description |
|---|---|
| **all($route, ...$handlers)** | Alias method which will react to all request methods |
| **get($route, ...$handlers)** | Alias method which will react to **GET** requests |
| **post($route, ...$handlers)** | Alias method which will react to **POST** requests |
| **head($route, ...$handlers)** | Alias method which will react to **HEAD** requests |
| **options($route, ...$handlers)** | Alias method which will react to **OPTIONS** requests |
| **patch($route, ...$handlers)** | Alias method which will react to **PATCH** requests |
| **put($route, ...$handlers)** | Alias method which will react to **PUT** requests |
| **delete($route, ...$handlers)** | Alias method which will react to **DELETE** requests |

#### $app->middleware($placing, ...$handlers) : \Monty\Application

This method registers additional handlers which will be executed without regard to the requesting method.  

##### Arguments

| Argument | Type | Description |
|---|---|---|
| $placing | *integer* | The request lifecycle position (Application::PREPEND -- before, Application::APPEND -- after) when the handlers should be executed. |
| ...$handlers | *callable[]* | Collection of handlers which will be executed. |

##### Example

```php
// ...
$app->middleware(
    \Monty\Application::PREPEND,
    function ($req, $res) { /*...*/ },
    function ($req, $res) { /*...*/ },
    function ($req, $res) { /*...*/ }
    // ...
);
// ...
```

##### Aliases

| Method | Description |
|---|---|
| **before(...$handlers)** | Alias method which will add request handlers executed **before** the actual request handling |
| **after(...$handlers)** |  Alias method which will add request handlers executed **after** the actual request handling |

#### $app->getRequest() : \Monty\Request

This method retrieves the current request object.
 
#### $app->getResponse() :  : \Monty\Response

This method retrieves the current response object.

## Request

### @TODO

## Response

### @TODO

## Contribute

### @TODO