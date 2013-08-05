<?php

use Inspirio\Deployer\Command\ComposerCommand;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$sourceDir  = __DIR__ . '/..';
$targetDir  = __DIR__;
$targetFile = 'deployer.phar.php';

@unlink($targetDir . '/' . $targetFile);

$iterator = Finder::create()
    ->in($sourceDir)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->notPath('/^build/')
    ->notPath('/^composer\./');

$phar = new Phar($targetDir . '/' . $targetFile);
$phar->buildFromIterator($iterator, $sourceDir);
//$phar->compressFiles(Phar::BZ2);
$phar->setStub(
    '<?php
    Phar::interceptFileFuncs();
    Phar::webPhar("deployer.phar");
    __HALT_COMPILER(); ?>'
);

function rmDirRecursive($dir)
{

}

function prepareFiles($sourceDir, $targetDir)
{
    if (file_exists($targetDir)) {
        rmDirRecursive($targetDir);
    }

    mkdir($targetDir);
    $copy = array(
        'public',
        'src',
        'view',
        'deployer.yml',
        'index.php',
    );

    foreach ($copy as $item) {
        copy($sourceDir . '/' . $item, $targetDir . '/' . $item);
    }
}

function installDependencies($sourceDir, $targetDir)
{
    $composer = new ComposerCommand($sourceDir);
}
