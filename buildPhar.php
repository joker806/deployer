<?php

$targetDir = __DIR__ .'/build';
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

@unlink($targetDir .'/deployer.phar.php');
$phar = new Phar($targetDir .'/deployer.phar.php');
$phar->buildFromDirectory('deployer');
$phar->setStub('<?php
Phar::interceptFileFuncs();
Phar::webPhar("deployer.phar");
__HALT_COMPILER(); ?>');

