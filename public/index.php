<?php
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Middleware\ModuleMiddleware;
use Inspirio\Deployer\Middleware\SecurityMiddleware;
use Inspirio\Deployer\Middleware\StarterMiddleware;
use Inspirio\Deployer\RequestHandler;
use Inspirio\Deployer\Security;
use Inspirio\Deployer\View\View;
use Symfony\Component\Debug\Debug;

require_once __DIR__ . '/../env.php';

// ensure Deployer is correctly installed
if (!file_exists(DEPLOYER_HOME_DIR . '/.installed')) {
    $error = require_once __DIR__ . '/../install.php';

    if ($error == 0) {
        touch(DEPLOYER_HOME_DIR . '/.installed');
        echo '<script>setTimeout(function() { window.location.reload(); }, 2000);</script>';
    }

    return;
}

// register class auto-loaders
require DEPLOYER_DIR . '/src/autoload.php';
require DEPLOYER_HOME_DIR .'/vendor/autoload.php';

// setup error handling
Debug::enable(E_ALL, DEPLOYER_ENV === 'development');

// load app
$app = require __DIR__ .'/../app.php';

// setup middlewares
$securityMw = new SecurityMiddleware(array(
    new Security\IpFilterSecurity(),
    new Security\HttpsSecurity(),
    new Security\StaticPassPhraseSecurity(),
));

$starterMw = new StarterMiddleware($app);
$moduleMw  = new ModuleMiddleware($app);

// handle the request
$config   = new Config('deployer.yml');
$view     = new View(__DIR__ . '/view');
$deployer = new RequestHandler($config, $view, $app);

$deployer
    ->addMiddleware($securityMw)
    ->addMiddleware($starterMw)
    ->addMiddleware($moduleMw)
;

$deployer->dispatch();
