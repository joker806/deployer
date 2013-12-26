<?php
require_once __DIR__ . '/env.php';

header('Content-type: text/html; charset=utf-8');

?>
<style>header { text-align: center; margin-bottom: 40px; }</style>
<header>
    <h1>Initializing Deployer</h1>
    <p>Please wait...</p>
</header>
<pre>
<?php

try {
    writeln('Initializing required environment.');
    clearHomeDir();
    downloadComposer();
    installDependencies();
    success();
    return 0;

} catch (\Exception $e) {
    writeln($e->getMessage(), 'red');
    return 1;
}

function clearHomeDir() {
    writeln('Clearing home directory.');

    if (file_exists(DEPLOYER_HOME_DIR)) {
        if (!is_dir(DEPLOYER_HOME_DIR)) {
            fail('Can\'t clear home directory \'%s\'. Path found, but is a file.', DEPLOYER_HOME_DIR);
        }

        runCommand('rm -rf '.DEPLOYER_HOME_DIR);
    }

    mkdir(DEPLOYER_HOME_DIR, 0777, true);
}

function downloadComposer() {
    writeln('Downloading Composer.');

    if (file_exists(DEPLOYER_HOME_DIR .'/composer.phar')) {
        return;
    }

    runCommand('php '. DEPLOYER_DIR .'/bin/installComposer --install-dir '. DEPLOYER_HOME_DIR);
}

function installDependencies() {
    writeln('Installing dependencies.');

    copy(DEPLOYER_DIR .'/composer.json', DEPLOYER_HOME_DIR . '/composer.json');
    copy(DEPLOYER_DIR .'/composer.lock', DEPLOYER_HOME_DIR . '/composer.lock');

    runCommand(
        'php '. DEPLOYER_HOME_DIR .'/composer.phar install --no-interaction --no-dev --optimize-autoloader --working-dir '. DEPLOYER_HOME_DIR,
        null,
        array(
            'COMPOSER_HOME' => DEPLOYER_HOME_DIR .'/.composer',
        )
    );
}

function fail($message = null)
{
    $args = func_get_args();
    $message = array_shift($args);

    if ($message === null) {
        $message = 'Unknown error. Initialization failed.';
    }

    throw new \Exception($message);
}

function success($message = null)
{
    if ($message === null) {
        $message = 'Environment initialized.';
    }

    writeln($message);
}


function writeln($text, $color = null)
{
    if ($color !== null) {
        $text = sprintf('<div style="color:%s">%s</div>', $color, $text);
    }

    echo $text . str_repeat(' ', 2048) . PHP_EOL;
    flush();
}

function runCommand($cmd, $input = null, array $env = array())
{
    $descriptors = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );

    writeln('> '.$cmd, 'blue');

    $process = proc_open($cmd, $descriptors, $pipes, DEPLOYER_HOME_DIR, $env);

    if (!is_resource($process)) {
        fail('Can\'t run command \'%s\'', $process);
    }

    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    writeln('< '. $output, 'gray');
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $result = proc_close($process);

    if ($result > 0) {
        writeln('< '. $error, 'red');
        fail($error);
    }

    return $result;
}

