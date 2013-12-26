<?php
use Inspirio\Deployer\Container;
use Inspirio\Deployer\RequestHandler;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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

// init container
$container = new Container(
    DEPLOYER_APP_DIR,
    DEPLOYER_DIR,
    DEPLOYER_DIR .'/deployer.yml'
);

// handle the request
$request = Request::createFromGlobals();

$session = new Session();
$request->setSession($session);

/** @var $requestHandler RequestHandler */
$requestHandler = $container['request_handler'];

$response = $requestHandler->handleRequest($request);
$response->send();
