<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Config\Config;

interface ConfigAwareModuleInterface {

    /**
     * Sets the module config.
     *
     * @param Config $config
     */
    public function setConfig(Config $config);
}
