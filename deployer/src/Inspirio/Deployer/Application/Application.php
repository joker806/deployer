<?php
namespace Inspirio\Deployer\Application;


abstract class Application implements ApplicationInterface {

    /**
     * @var string
     */
    protected  $appDir;

    /**
     * {@inheritdoc}
     */
    public function __construct($appDir) {
        $this->appDir = realpath($appDir);

        if ($this->appDir === false) {
            throw new \RuntimeException("Application directory '{$appDir}' does not exist.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRootPath() {
        return $this->appDir;
    }

}
