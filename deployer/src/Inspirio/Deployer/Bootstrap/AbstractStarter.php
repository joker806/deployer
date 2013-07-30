<?php
namespace Inspirio\Deployer\Bootstrap;


use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;

abstract class AbstractStarter implements StarterModuleInterface, ConfigAware
{
    /**
     * @var ApplicationInterface
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function setApp(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
