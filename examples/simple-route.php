<?php
chdir(__DIR__ . '/../');
require_once getcwd() . '/vendor/autoload.php';

$app = new \Monty\Application();

$app->get(
    '/index',
    function (\Monty\Request $req, \Monty\Response $res) {
        return $res;
    }
);