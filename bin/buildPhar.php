#!/usr/bin/env php
<?php

use Inspirio\Deployer\Command\ComposerCommand;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$sourceDir  = __DIR__ .'/..';
$targetDir  = '.';
$targetFile = 'deployer.phar.php';

@unlink($targetDir .'/'. $targetFile);

$iterator = Finder::create()
    ->in($sourceDir)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->notPath('/^bin/')
    ->notPath('/^project/')
    ->notPath('/^vendor/')
    ->notPath('/^composer\./')
;

$phar = new Phar($targetDir .'/'. $targetFile);
$phar->buildFromIterator($iterator, $sourceDir);
//$phar->compressFiles(Phar::BZ2);
$phar->setStub('<?php
Phar::interceptFileFuncs();
Phar::webPhar("deployer.phar.php", "public/index.php");
__HALT_COMPILER(); ?>');
