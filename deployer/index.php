<?php

use Inspirio\Deployer\Handler\CliHandler;
use Inspirio\Deployer\Handler\WebHandler;

require 'vendor/autoload.php';

$modules = array(
    new \Inspirio\Deployer\Module\Info\InfoModule(),
    new \Inspirio\Deployer\Module\Deployment\DeploymentModule(),
    new \Inspirio\Deployer\Module\Configuration\ConfigurationModule(),
    new \Inspirio\Deployer\Module\Maintenance\MaintenanceModule(),
);

$security = array(
    new \Inspirio\Deployer\Security\IpFilterSecurity(),
    new \Inspirio\Deployer\Security\HttpsSecurity(),
    new \Inspirio\Deployer\Security\StaticPassPhraseSecurity(),
);

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('html_errors', 'On');

$dirName = __DIR__;

if (substr($dirName, 0, 7) === 'phar://') {
    $dirName = substr($dirName, 7);
    $dirName = dirname($dirName);
}

$dirName = __DIR__ . '/../project';

if (PHP_SAPI === 'cli') {
    $controller = new CliHandler($dirName, $modules);
} else {
    $controller = new WebHandler($dirName, $modules, $security);
}

echo $controller->dispatch();
