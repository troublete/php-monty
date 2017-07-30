<?php

/**
 * Class RestHandler
 */
class RestHandler
{
    /**
     * @var \Monty\Request
     */
    protected $request;

    /**
     * @var \Monty\Response
     */
    protected $response;

    /**
     * @param \Monty\Request $req
     * @param \Monty\Response $res
     */
    public function __invoke(
        \Monty\Request $req,
        \Monty\Response $res
    )
    {
        $this->request = $req;
        $this->response = $res;

        $methodName = strtolower($req->requestMethod());

        if (method_exists($this, $methodName))
            return $this->{$methodName}(...$req->routeParameters());

        return $res;
    }

    public function get()
    {
        return new \Monty\Response('GET');
    }

    public function put()
    {
        return new \Monty\Response('PUT');
    }
}