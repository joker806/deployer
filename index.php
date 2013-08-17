<?php

use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\RequestHandler;
use Inspirio\Deployer\View\View;

require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('html_errors', 'On');

$appDir = __DIR__;

// PHAR environment
if (substr($appDir, 0, 7) === 'phar://') {
    $appDir = substr($appDir, 7);
    $appDir = dirname($appDir);

// development environment
} else {
    $appDir = $appDir . '/../project';

    if (!file_exists($appDir)) {
        mkdir($appDir);
    }
}

$app = new \Inspirio\Deployer\Application\LazyCms3($appDir);

$config   = new Config('deployer.yml');
$view     = new View(__DIR__ . '/view');
$deployer = new RequestHandler($config, $view, $app);

$securityMw = new \Inspirio\Deployer\Middleware\SecurityMiddleware(array(
    new \Inspirio\Deployer\Security\IpFilterSecurity(),
    new \Inspirio\Deployer\Security\HttpsSecurity(),
    new \Inspirio\Deployer\Security\StaticPassPhraseSecurity(),
));

$starterMw = new \Inspirio\Deployer\Middleware\StarterMiddleware($app);
$moduleMw  = new \Inspirio\Deployer\Middleware\ModuleMiddleware($app);

$deployer
    ->addMiddleware($securityMw)
    ->addMiddleware($starterMw)
    ->addMiddleware($moduleMw);

echo $deployer->dispatch();
