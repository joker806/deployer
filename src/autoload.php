<?php

spl_autoload_register(function ($class) {
    $pos = strrpos($class, '\\');

    // non-namespaces class
    if ($pos === false) {
        return null;
    }

    $classPath = strtr(substr($class, 0, $pos), '\\', DIRECTORY_SEPARATOR);
    $className = substr($class, $pos + 1);

    $classPath = __DIR__ . DIRECTORY_SEPARATOR . $classPath . DIRECTORY_SEPARATOR . $className . '.php';

    if (!file_exists($classPath)) {
        return null;
    }

    include $classPath;
}, true);
