<?php
chdir(__DIR__ . '/../');
require_once getcwd() . '/vendor/autoload.php';
require_once __DIR__ . '/RestHandler.php';

$app = new \Monty\Application();

$app->all(
    '/index',
    RestHandler::class
);