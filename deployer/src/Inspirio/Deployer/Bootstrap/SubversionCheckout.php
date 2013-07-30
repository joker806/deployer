<?php
namespace Inspirio\Deployer\Bootstrap;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;

class SubversionCheckout implements StarterModuleInterface {

    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * {@inheritdoc}
     */
    function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function isReady()
    {
        return file_exists($this->app->getRootPath() .'/.svn');
    }

    /**
     * {@inheritdoc}
     */
    public function startupApp()
    {

    }

    public function setConfig(Config $config)
    {
        // TODO: Implement setConfig() method.
    }
}
