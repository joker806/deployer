<?php
namespace Inspirio\Deployer\DeploymentModule;

use Inspirio\Deployer\ModuleInterface;

/**
 * Action interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface DeploymentModuleInterface extends ModuleInterface
{
    /**
     * Checks if the action is enabled.
     *
     * @return bool
     */
    public function isEnabled();
}
