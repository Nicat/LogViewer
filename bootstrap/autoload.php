<?php
define('APP_START', microtime(true));
ini_set('memory_limit', '10240M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Register The Composer Auto Loader */
require __DIR__ . '/../vendor/autoload.php';

/* Cache autoload */
use phpFastCache\CacheManager;

// Setup File Path on your config files
CacheManager::setDefaultConfig([
    "path" => __DIR__ . '/../storage/cache/',
]);

/* Models */
foreach (glob(__DIR__ . "/../app/Models/*.php") as $filename) {
    include_once $filename;
}

/* Register The Router Auto Loader */
$router = new \Phroute\Phroute\RouteCollector();

foreach (glob(__DIR__ . "/../router/*.php") as $filename) {
    include_once $filename;
}

//var_dump($router->getData()->getStaticRoutes());
//die;