<?php

require '../vendor/autoload.php';

session_cache_limiter(false);
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

$slim = new \Slim\Slim(
    [
        'templates.path' => '../src/templates/'
    ]
);

$container = new \Ccs\Container($slim);

$slim->run();