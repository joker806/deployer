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
    $appDir = $appDir .'/../project';

    if (!file_exists($appDir)) {
        mkdir($appDir);
    }
}

$app = new \Inspirio\Deployer\Application\LazyCms3($appDir);

$app
    ->addModule(new \Inspirio\Deployer\Module\Info\InfoModule())
    ->addModule(new \Inspirio\Deployer\Module\Deployment\DeploymentModule())
    ->addModule(new \Inspirio\Deployer\Module\Configuration\ConfigurationModule())
    ->addModule(new \Inspirio\Deployer\Module\Maintenance\MaintenanceModule())
    ->addModule(new \Inspirio\Deployer\Module\Database\DatabaseModule())
;

$config   = new Config('deployer.yml');
$view     = new View(__DIR__ .'/view');
$deployer = new RequestHandler($config, $view, $app);

$deployer
    ->addSecurity(new \Inspirio\Deployer\Security\IpFilterSecurity())
    ->addSecurity(new \Inspirio\Deployer\Security\HttpsSecurity())
    ->addSecurity(new \Inspirio\Deployer\Security\StaticPassPhraseSecurity())
;

echo $deployer->dispatch();
