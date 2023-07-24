<?php
require __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \App\Utils\Common;
use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;
use \App\Http\Middleware\Queue as MiddlewareQueue;

Environment::load(__DIR__.'/../');

Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

define('URL', getenv('URL'));

Common::init();

View::init([
    'URL' => URL
]);

MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'api' => \App\Http\Middleware\Api::class,
]);

MiddlewareQueue::setDefault([
    'maintenance'
]);