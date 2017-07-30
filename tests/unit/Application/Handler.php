<?php
class Handler
{
    public function __invoke($req, \Monty\Response $res)
    {
        $res->setContent('Class');
        return $res;
    }
}