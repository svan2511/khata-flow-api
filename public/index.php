<?php

// PHP < 8.4 polyfill for Symfony 8's request_parse_body() (PUT/PATCH body
// parsing). Must load before Request::capture() below. No-op on PHP 8.4+.
require __DIR__.'/../app/Support/request_parse_body_polyfill.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
