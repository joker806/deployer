<?php

use Inspirio\Deployer\Handler\CliHandler;
use Inspirio\Deployer\RequestHandler;

require 'vendor/autoload.php';

$modules = array(
    new \Inspirio\Deployer\Module\Info\InfoModule(),
    new \Inspirio\Deployer\Module\Deployment\DeploymentModule(),
    new \Inspirio\Deployer\Module\Configuration\ConfigurationModule(),
    new \Inspirio\Deployer\Module\Maintenance\MaintenanceModule(),
    new \Inspirio\Deployer\Module\Database\DatabaseModule(),
);

$security = array(
    new \Inspirio\Deployer\Security\IpFilterSecurity(),
    new \Inspirio\Deployer\Security\HttpsSecurity(),
    new \Inspirio\Deployer\Security\StaticPassPhraseSecurity(),
);

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
    $appDir = $appDir .'/../project';

    if (!file_exists($appDir)) {
        mkdir($appDir);
    }
}

$app = new \Inspirio\Deployer\Application\LazyCms2($appDir);
$controller = new RequestHandler(__DIR__, $app, $modules, $security);

echo $controller->dispatch();
