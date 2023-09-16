<?php
date_default_timezone_set('Asia/Seoul');
require __DIR__.'/../vendor/autoload.php';

use \App\Utils\Common;
use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;

Environment::load(__DIR__.'/../');

Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

define('URL', getenv('URL'));
define('ONESIGNAL_APP', getenv('ONESIGNAL_APP'));
define('ONESIGNAL_API_KEY', getenv('ONESIGNAL_API_KEY'));

Common::init();