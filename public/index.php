<?php
use Inspirio\Deployer\Container;
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

// init container
$container = new Container(
    DEPLOYER_APP_DIR,
    DEPLOYER_DIR,
    DEPLOYER_DIR .'/deployer.yml'
);

// handle the request
$container['request_handler']->dispatch();
