<?php
require_once __DIR__ . '/env.php';

if (!isCli()) {
    header('Content-type: text/html; charset=utf-8');
    echo <<<EOF
<style>header { text-align: center; margin-bottom: 40px; }</style>
<header>
    <h1>Initializing Deployer</h1>
    <p>Please wait...</p>
</header>
<pre>
EOF;
}

try {
    writeln('Initializing required environment.');
    clearHomeDir();
    downloadComposer();
    installDependencies();
    success();

    return 0;

} catch (\Exception $e) {
    $message = $e->getMessage();
    $message = "\033[0;31m" . $message . "\033[0m";
    array_unshift($args, $message);

    call_user_func_array('writeln', $args);

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

    runCommand('php '. DEPLOYER_HOME_DIR .'/composer.phar install --no-interaction --no-dev --optimize-autoloader --working-dir '. DEPLOYER_HOME_DIR);
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

    if (!isCli()) {
        writeln('<script>setTimeout(function() { window.location.reload(); }, 2000);</script>');
    }
}


function writeln($text)
{
    if (func_num_args() > 1) {
        $text = call_user_func_array('sprintf', func_get_args());
    }

    echo $text;

    if (!isCli()) {
        echo str_repeat(' ', 2048);
    }

    echo PHP_EOL;
    flush();
}

function runCommand($cmd, $input = null, &$output = null, &$error = null)
{
    $descriptors = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );

    $process = proc_open($cmd, $descriptors, $pipes, DEPLOYER_HOME_DIR);

    if (!is_resource($process)) {
        fail('Can\'t run command \'%s\'', $process);
    }

    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    return proc_close($process);
}

function isCli() {
    return php_sapi_name() === "cli";
}
