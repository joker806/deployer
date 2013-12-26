<?php

$appDir = __DIR__;

// PHAR environment
if (substr($appDir, 0, 7) === 'phar://') {
    $appDir = substr($appDir, 7); // strip phar:// prefix
    $appDir = dirname($appDir);   // strip deployer.phar suffix

    $env = 'production';

// development environment
} else {
    $appDir = $appDir . '/project';

    if (!file_exists($appDir)) {
        mkdir($appDir);
    }

    $env = 'development';
}

define('DEPLOYER_ENV',      $env);
define('DEPLOYER_DIR',      __DIR__);
define('DEPLOYER_APP_DIR',  $appDir);
define('DEPLOYER_HOME_DIR', $appDir .'/.deployer');
