<?php
namespace Inspirio\Deployer\Config;


interface ConfigAware
{
    /**
     * Sets config instance.
     *
     * @param Config $config
     */
    public function setConfig(Config $config);
}
